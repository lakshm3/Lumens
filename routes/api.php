<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("storeData","EngineController@storeData");
Route::get("hardwares","Api\EngineController@index");
Route::apiResource("engines","Api\EngineController");
Route::apiResource("interfaces","Api\UserInterfaceController");
Route::apiResource("engines/{engine}/datas","Api\EngineDataController");
Route::apiResource("settings","Api\SettingsController");
Route::post("login","Api\LoginController@login");
