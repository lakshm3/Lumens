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
Route::group(["middleware" => ["checkhost"]], function(){

Route::get('/api/{any}', function(){
    abort(404);
})->where('any', '.*');
Route::get('/{any}', 'SpaController@index')->where('any', '.*');

});