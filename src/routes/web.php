<?php

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

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Scriptologia\Chat\Http\Controllers'], function () {
    Route::get('/chat', 'ChatController@index')->name('chat');
    Route::get('/api/chat/get-me', 'ChatController@getMe');
    Route::get('/api/chat/contacts', 'ChatController@contacts');
    Route::get('/api/chat/all-users', 'ChatController@allUsers');
    Route::post('/api/chat/search', 'ChatController@search');
    Route::post('/api/chat/send-message', 'ChatController@sendMessage');
    Route::post('/api/chat/read-message', 'ChatController@readMessage');
    Route::post('/api/chat/trash-message', 'ChatController@trashMessage');
    Route::post('/api/chat/delete-chat', 'ChatController@deleteChat');
});
