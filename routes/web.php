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

/** Helper Functions - Will need to be moved to another location */
function scrubSalesforceFieldName($field) {
    $field = preg_replace(['/__[cr]$/'], [''], $field);
    $field = strtolower($field);
    return $field;
}

function scrubSalesforceFields($object) {
    if (!is_array($object)) return $object;

    foreach ($object as $key => $value) {
        unset($object[$key]);
        $object[scrubSalesforceFieldName($key)] = scrubSalesforceFields($value);
    }

    unset($object['attributes']);
    $object = renameFields($object);

    return $object;
}

function renameFields($object) {
    static $fieldMapping = ['owner' => 'contact'];

    if (!is_array($object)) return $object;

    foreach ($object as $key => $value) {
        if (!array_key_exists($key, $fieldMapping)) continue;

        unset($object[$key]);
        $object[$fieldMapping[$key]] = $value;
    }

    return $object;
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/assignments', function () {
    //return new AssignmentCollection(Assignment::all());
    //return Cache::remember(url('/assignments'), 3, function(){
        $SOQL = "SELECT Id, Name, Pipeline_State__c, Start_Date__c, End_Date__c,
                   Client__r.Name, Client__r.City__c, Client__r.State__c, Client__r.ZipCode__c,
                   Primary_Worksite__r.Name, Primary_Worksite__r.City__c, Primary_Worksite__r.State__c, Primary_Worksite__r.ZipCode__c, Primary_Worksite__r.Country__c,
                   Owner.Name, Owner.Title, Owner.Phone, Owner.Email
                 FROM Assignment__c
                 LIMIT 5";

        $results = Forrest::query($SOQL);
        $return = ['total_size' => $results['totalSize'], 'assignments' => []];

        foreach ($results['records'] as $key => $value) {
            $return['assignments'][] = scrubSalesforceFields($value);
        }

        return $return;
    //});
});

Route::get('/authenticate', function()
{
    Forrest::authenticate();
    return Redirect::to('/assignments');
});

Route::get('/callback', function()
{
    Forrest::callback();

    //return Redirect::to('/');
});

