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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', ['uses' => 'MainController@index']);

Route::get('/info', ['uses' => 'MainController@info']);

Route::any('/decode', ['uses' => 'MainController@decode']);

Route::get('/redis/info', ['uses' => 'RedisController@info']);

Route::get('/redis/treeKeys/{key?}', ['uses' => 'RedisController@treeKeys']);

Route::get('/redis/', ['uses' => 'RedisController@index']);

Route::get('/redis/get/{key}', ['uses' => 'RedisController@get']);

Route::get('/redis/raw/{cmd}', ['uses' => 'RedisController@raw']);