<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    $array = ["message" => "Welcome to Sacred Heart Monastery REST Service!",  "success" => true, "code" => 200, "time" => \Carbon\Carbon::now()];
    return response()->json($array);
});

/**
 * Login Route
 */
$app->post('auth/login', 'AuthController@postLogin');

$app->group(['middleware' => 'auth'], function () use($app) {

    /**
     * Auth Route
     */
    $app->post('auth/logout', 'AuthController@postLogout');
    $app->put('auth/display_name', 'AuthController@putDisplayName');
    $app->put('auth/password', 'AuthController@putPassword');
    $app->post('auth/latest', 'AuthController@getLatest');
    $app->post('auth/history', 'AuthController@getHistory');

    /**
     * Diocese Route
     */
    $app->post('diocese/fetch_all', 'DioceseController@listDiocese');
});

