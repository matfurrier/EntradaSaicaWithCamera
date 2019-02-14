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

Auth::routes();

Route::get('/clear', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('route:clear');
    echo 'Cache eliminada y reconfigurada!!';
});

Route::get('/link', function () {
    $exitCode = Artisan::call('storage:link');
    echo 'Storage Link';
});

Route::get('/t', function () {

    $data = \App\PersonCheck::all();

    $times = [];

    try {
        for ($i = 0; $i<= count($data) - 1; $i++) {

            $times[] = \Carbon\Carbon::parse($data[$i+1]['moment'])->diffInHours(\Carbon\Carbon::parse($data[$i]['moment'])) ;

            $i = $i + 1;

            if (($i + 1) > count($data) - 1) { $i = count($data) - 1;}

        }
    } catch (Exception $e) {

    }

    $total = collect($times)->reduce(function ($carry, $item) {
        return $carry + $item;
    });

    echo $total;
});

Route::middleware('auth')->group(function () {

    Route::get('/back', 'HomeController@index')->name('home');

    Route::get('/persons', 'PersonsController@index')->name('persons');

    Route::get('/users', 'UsersController@index')->name('users');

    Route::get('/company', 'CompanyController@index')->name('company');

    Route::get('/divisions', 'DivisionsController@index')->name('divisions');

    Route::get('/divisions/div/{id?}', 'PersonDivController@index')->name('div');

    Route::get('/rols', 'RolsController@index')->name('rols');

    Route::get('/motives', 'MotivesController@index')->name('motives');

    Route::get('/checks/{id?}', 'CheckController@index')->name('check');

    Route::get('/report', 'ReportController@index')->name('report');

    Route::post('/report/xls', 'ReportController@export')->name('reportxls');

});
