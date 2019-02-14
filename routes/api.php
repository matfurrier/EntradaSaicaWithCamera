<?php

use Illuminate\Http\Request;

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

// Compañia

Route::get('/company/list', 'CompanyController@getList');
Route::resource('/company', 'CompanyController');


// RUTAS DE SUCURSALES
Route::prefix('divisions')->group(function () {

    Route::get('list', 'DivisionsController@getList');

    Route::get('data/{token}', 'DivisionsController@data');


});
Route::resource('/divisions', 'DivisionsController');

// RUTAS DE ROLES
Route::prefix('rols')->group(function () {

    Route::get('list', 'RolsController@getList');


});
Route::resource('/rols', 'RolsController');


// RUTAS DE MOTIVOS
Route::prefix('motives')->group(function () {

    Route::get('list', 'MotivesController@getList');

    Route::get('motives', 'MotivesController@getMotives');


});
Route::resource('/motives', 'MotivesController');


// RUTAS DE TRABAJADORES
Route::prefix('persons')->group(function () {

    Route::get('list', 'PersonsController@getList');

    Route::post('check', 'PersonsController@check');

});
Route::resource('/persons', 'PersonsController');


// RUTAS DE TRABAJADORES
Route::prefix('checks')->group(function () {

    Route::get('list', 'CheckController@getList');

});
Route::resource('/checks', 'CheckController');


// RUTAS DE TRABAJADORES
Route::prefix('divs')->group(function () {

    Route::get('list', 'PersonDivController@getList');

});
Route::resource('/divs', 'PersonDivController');



// REPORT CONTROLLER
Route::prefix('reports')->group(function () {

    Route::get('list', 'ReportController@getList');

    Route::post('pdf', 'ReportController@pdf');

});
Route::resource('/reports', 'ReportController');


// RUTAS DE USUER SISTEMA
Route::prefix('users')->group(function () {

    Route::get('list', 'UsersController@getList');


});
Route::resource('/users', 'UsersController');
