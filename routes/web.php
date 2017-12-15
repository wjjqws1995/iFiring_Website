<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Home Page
Route::get('/', function () {
    return view('welcome');
});

//Blog Page
Route::get('blog','BlogController@index');

Route::get('blog/{slug}','BlogController@showPost');

//Admin Page
Route::get('admin',function (){
    return redirect('/admin/post');
});

Route::group(['namespace'=>'Admin','middleware'=>'auth'],function (){
    Route::resource('admin/post','PostController');
    Route::resource('admin/tag','TagController');
    Route::get('admin/upload','UploadController@index');
});

//Login and out
Route::group(['namespace'=>'Auth'],function (){
//    Route::get('/login','LoginController@showLoginForm');

//    Route::post('/login','LoginController');
//
//    Route::get('/logout','LoginController');
});