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

Route::post('/ewallet/ping', 'MyController@ping');
Route::post('/ewallet/register', 'MyController@register');
Route::post('/ewallet/getSaldo/', 'MyController@getSaldo');
Route::post('/ewallet/getTotalSaldo', 'MyController@getTotalSaldo');
Route::post('/ewallet/transfer', 'Myntroller@transfer');