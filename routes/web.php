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

$app->group(['prefix' => 'v1'], function() use($app) {
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
        $app->group(['prefix' => 'auth'], function() use($app) {
            $app->post('logout', 'AuthController@postLogout');
            $app->put('display_name', 'AuthController@putDisplayName');
            $app->put('password', 'AuthController@putPassword');
            $app->post('latest', 'AuthController@getLatest');
            $app->post('history', 'AuthController@getHistory');
        });

        /**
         * Diocese Route
         */
        $app->post('diocese/fetch_all', 'DioceseController@listDiocese');

        /**
         * Province route
         */
        $app->group(['prefix' => 'provinces'], function() use($app) {
            $app->get('fetch_all', 'ProvinceController@getListProvince');
        });

        /**
         * District route
         */
        $app->group(['prefix' => 'districts'], function() use($app) {
            $app->get('fetch_all', 'DistrictController@getListDistrict');
            $app->get('get_by_province', 'DistrictController@getByProvince');
            $app->get('single', 'DistrictController@getSingleDistrict');
        });

        /**
         * Parish Route
         */
        $app->group(['prefix' => 'parish'], function() use($app) {
            $app->post('fetch_all', 'ParishController@listParish');
            $app->post('create', 'ParishController@createParish');
            $app->put('update', 'ParishController@updateParish');
            $app->delete('remove', 'ParishController@updateParish');
            $app->delete('remove_all', 'ParishController@updateParish');
        });
    });
});