<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contacts;
use Validator;
use Illuminate\Routing\UrlGenerator;
use file;

class ContactsController extends Controller
{
    protected $contacts;
    protected $base_url;

    public function __Construct(UrlGenerator $urlGenerator){
        $this->middleware('auth:users');
        $this->contacts = new Contacts;
        $this->base_url = $urlGenerator->to('/');
    }

    public function addContact(Request $request){
        $validator = validator::make($request->all(),
        [
            "token"=>"required",
            "firstname"=>"required|string",
            "lastname"=>"required|string"
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->messages()->toArray()
            ], 500);
        }

        $profile_picture = $request->profile_image;
        $file_name="";
        if($profile_picture == null){
            $file_name = 'default-avatar.png';
        }
        else{
            $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
            $base64Image = $profile_picture;
            $fileBin = file_get_contents($base64Image);
            $mimetype = mime_content_type($base64Image);
            if("image/png"==$mimetype){
                $file_name = $generate_name.".png";
            }
            else if("image/jpeg"==$mimetype){
                $file_name = $generate_name.".jpeg";
            }
            else if("image/jpg"==$mimetype){
                $file_name = $generate_name.".jpg";
            }
            else{
                return response()->json([
                    'success'=>false,
                    'message'=>'Invalid image format'
                ], 500);
            }
        }

        $user_token = $request->token;
        $user = auth("users")->authenticate($user_token);
        $user_id = $user->id;

        $this->contacts->user_id = $user_id;
        $this->contacts->phone_number = $request->phone_number;
        $this->contacts->firstname = $request->firstname;
        $this->contacts->lastname = $request->lastname;
        $this->contacts->email = $request->email;
        $this->contacts->image_file = $file_name;
        $this->contacts->save();

        if($profile_picture == null){

        }
        else{
            file_put_contents("./profile_images/".$file_name, $fileBin);
        }
        return response()->json([
            'success'=>true,
            'message'=>'Contact successfully created'
        ], 200);
    }

    public function getpaginatedData($token, $pagination=null){
        $file_directory = $this->base_url."/profile_images";
        $user = auth('users')->authenticate($token);
        $user_id = $user->id;
        if($pagination == null || $pagination == ''){
            $contacts = $this->contacts->where('user_id',$user_id)->orderBy('id', 'DESC')
            ->get()->toArray();

            return response()->json([
                'success'=>true,
                'data'=>$contacts,
                'file_directory'=>$file_directory
            ], 200);
        }
            $contacts_paginated = $this->contacts->where('user_id',$user_id)->orderBy('id', 'DESC')
            ->paginate($pagination);

            return response()->json([
                'success'=>true,
                'data'=>$contacts_paginated,
                'file_directory'=>$file_directory
            ], 200);
        
    }


    //Edit Contact
    public function editSingleData(Request $request, $id)
    {
        $validator = validator::make($request->all(),
        [
            "firstname"=>"required|string",
            "lastname"=>"required|string"
        ]);

        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$validator->messages()->toArray()
            ], 500);
        }
        
        $findData = $this->contacts::find($id);
      
        if(!$findData){
           
                return response()->json([
                    'success'=>false,
                    'message'=>'Contact does not exist'
                ], 500);
            }
           
            $getFile = $findData->image_file;
           // $getFile=='default-avata.png'? :File::delete('./profile_images/'.$getFile);
          

        $profile_picture = $request->profile_image;
        $file_name="";
        if($profile_picture == null){
            $file_name = 'default-avatar.png';
        }
        else{
            $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
            $base64Image = $profile_picture;
            $fileBin = file_get_contents($base64Image);
            $mimetype = mime_content_type($base64Image);
            if("image/png"==$mimetype){
                $file_name = $generate_name.".png";
            }
            else if("image/jpeg"==$mimetype){
                $file_name = $generate_name.".jpeg";
            }
            else if("image/jpg"==$mimetype){
                $file_name = $generate_name.".jpg";
            }
            else{
                return response()->json([
                    'success'=>false,
                    'message'=>'Invalid image format'
                ], 500);
            }
        }

 
        $findData->phone_number = $request->phone_number;
        $findData->firstname = $request->firstname;
        $findData->lastname = $request->lastname;
        $findData->email = $request->email;
        $findData->image_file = $file_name;
        $findData->save();

        if($profile_picture == null){

        }
        else{
            file_put_contents("./profile_images/".$file_name, $fileBin);
        }
        return response()->json([
            'success'=>true,
            'message'=>'Contact successfully updated'
        ], 200);


        
    }


    //Delete Contact

    public function deleteContacts($id)
    {
        $findData = $this->contacts::find($id);
        if(!$findData){
            
                return response()->json([
                    'success'=>false,
                    'message'=>'Contact does not exist'
                ], 500);
            
        }
            $getFile = $findData->image_file;
            if($findData->delete()){
                //$getFile == "default-avatar.png"? :unlink('./profile_images/'.$getFile);

                return response()->json([
                    'success'=>true,
                    'message'=>'Contact deleted'
                ], 200 );
            }
        
    }


    public function getSingleData($id)
    {
        $file_directory = $this->base_url."/profile_images";
        $findData = $this->contacts::find($id);
        if(!$findData){
            if($validator->fails()){
                return response()->json([
                    'success'=>false,
                    'message'=>'Contact does not exist'
                ], 500);
                
            }
        }
            return response()->json([
                'success'=>true,
                'data'=>$findData,
                'file_directory'=>$file_directory
            ], 200);
        
    }

    //function to search for data

    public function searchData($search, $token, $pagination=null)
    {
        $file_directory = $this->base_url.'/profile_images';
        $user = auth('users')->authenticate($token);
        $user_id = $user->id;

        if($pagination==null || $pagination==''){
            $non_paginated_search_query = $this->contacts::where('user_id', $user_id)
            ->where(function($query) use ($search){
                $query->where('firstname', 'LIKE', "%$search%")->orWhere('lastname', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")->orWhere('phone_number', 'LIKE', "%$search%");
            })->orderBy('id', 'DESC')->get()->toArray();
            return response()->json([
                'success'=>true,
                'data'=>$non_paginated_search_query,
                'file_directory'=>$file_directory
            ], 200);
        }

        $paginated_search_query = $this->contacts::where('user_id', $user_id)
            ->where(function($query) use ($search){
                $query->where('firstname', 'LIKE', "%$search%")->orWhere('lastname', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")->orWhere('phone_number', 'LIKE', "%$search%");
            })->orderBy('id', 'DESC')->paginate($pagination);
            return response()->json([
                'success'=>true,
                'data'=>$paginated_search_query,
                'file_directory'=>$file_directory
            ], 200);

    }
}
