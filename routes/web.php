<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


Auth::routes();
Route::get('/', 'ChatController@index')->name('home');
Route::get('/settings', 'ChatController@settings')->name('settings');
Route::post('/edit', 'ChatController@edit')->name('edit');
Route::post('/change-password', 'ChatController@change_password')->name('change.password');
Route::post('/send/message','system\SendController@index')->name('send');
Route::post('/get','system\GetDataController@index');
Route::post('/delete','system\SendController@delete');
Route::post('/delete/pv','system\SendController@delete_pv');
Route::post('/deleted','system\SendController@deleted');
Route::get('/download','system\GetDataController@download')->name('download');
Route::post('/search','ChatController@search')->name('search');
Route::get('/settings/pro', 'UserProfileController@show')->name('profile.show');
Route::post('/settings/profile', 'UserProfileController@update')->name('profile.update');



