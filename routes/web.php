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
    return 'PING';
});

/**
 * Login Route
 */
$app->post('auth/login', 'AuthController@postLogin');

$app->group(['middleware' => 'auth'], function ($app) {

    /**
     * Auth Route
     */
    $app->post('auth/logout', 'AuthController@postLogout');
    $app->put('auth/display_name', 'AuthController@putDisplayName');
    $app->put('auth/password', 'AuthController@putPassword');
    $app->get('auth/lastest', 'AuthController@getLastest');
});

