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

Route::get('/ewallet/ping', 'MyController@ping');
Route::get('/ewallet/register', 'MyController@register');
Route::get('/ewallet/getSaldo', 'MyController@getSaldo');
Route::get('/ewallet/getTotalSaldo', 'MyController@getTotalSaldo');
Route::get('/ewallet/transfer', 'MyController@transfer');