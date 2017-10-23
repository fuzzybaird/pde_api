<?php

use App\Assignment;
use App\Http\Resources\AssignmentCollection;

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

Route::get('/assignments', function () {
    //return new AssignmentCollection(Assignment::all());
    //return Cache::remember(url('/assignments'), 3, function(){
        $results = Forrest::query('SELECT Id, Name FROM Assignment__c LIMIT 5');

        $return = ['total_size' => $results['totalSize'], 'results' => []];

        foreach ($results['records'] as $key => $value) {
            $return['results'][] = ['id' => $value['Id']];
        }

        return $return;
    //});
});

Route::get('/authenticate', function()
{
    return Forrest::authenticate();
});

Route::get('/callback', function()
{
    Forrest::callback();

    //return Redirect::to('/');
});

