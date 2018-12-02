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

Route::get('/', function () {
    return view('welcome');
});

//route for verification
Route::get("/zechbot", "MainController@receive")->middleware("verify");

//where Facebook sends messages to. No need to attach the middleware to this because the verification is via GET
Route::post("/zechbot", "MainController@receive");
