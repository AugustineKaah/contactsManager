<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix'=>'user',
    'namespace'=>'user'
],

function(){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('contact/add', 'ContactsController@addContact');
    Route::get('contact/get-all/{token}/{pagination?}', 'ContactsController@getPaginatedData');
    Route::post('contact/update/{id}', 'ContactsController@editSingleData');
    Route::post('contact/delete/{id}', 'ContactsController@deleteContacts');
    Route::get('contact/get-single/{id}', 'ContactsController@getSingleData');
    Route::get('contact/search/{search}/{token}/{pagination?}', 'ContactsController@searchData');
}
);
