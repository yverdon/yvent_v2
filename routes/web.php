<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('register2', 'Auth\Register2Controller@showRegistrationForm');
Route::post('register2', 'Auth\Register2Controller@register');

Route::get('/', function () {
    $data = [
        'page_title' => 'Calendrier',
    ];
    return view('event/index', $data);
})->middleware('auth');


// Provide controller methods with object instead of ID (not necessary?)
Route::model('slots', 'Slot');

// Register events routes
Route::resource('events', 'EventController');
Route::get('events/create/{eventtype}', 'EventController@create');
Route::get('events/{eventtype}/{listtype}', 'EventController@index');

// Register slots routes
Route::resource('events.slots', 'SlotController');

// Register slots routes
Route::resource('events.logs', 'LogController');

// Register documents routes
Route::resource('events.documents', 'DocumentController');
Route::get('doc/{id}', 'DocumentController@doc');

// Get Events for calendar as JSON
Route::get('api2/{eventtype}', 'SlotController@api2');

// Get Events for calendar as ICAL
Route::get('ical/{key}/yvent_{username}.ics', 'SlotController@ical');

// Redirect after registration
Route::get('/home', 'HomeController@index');

Route::get('/getdata/{term}', 'EventController@getdata');

// Get works list
Route::get('/chantiers', 'EventController@index_works');
Route::get('/chantiers/pdf', 'EventController@index_works_pdf');
Route::get('/chantiers/aggloy', 'EventController@index_works_aggloy');
Route::get('/chantiers/aggloy/{service}', 'EventController@index_works_aggloy');
Route::get('/chantiers/{service}', 'EventController@index_works');

// URB_planif_projectController
Route::get('/URB_planif_project/{id}', 'URB_planif_projectController@show');

// SDIS_tube_clefController
Route::get('/SDIS_tube_clef/{id}', 'SDIS_tube_clefController@show');

// POLADM_etablissementController
Route::get('/POLADM_etablissement/{id}', 'POLADM_etablissementController@show');

// ADR_axe
Route::get('/rues', 'ADR_axeController@index');

// Investments
Route::get('/pi', 'InvestmentController@index');

// Event TNT fulltextSearch
Route::get('/eventfulltext', 'EventController@event_fulltextsearch');
