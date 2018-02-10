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


$app->get('/', function () {
    return redirect('/v1');
});

/**
 * REST API Routing
 */

$app->group(['prefix' => 'v1'], function() use($app) {
    $app->get('/', function () use ($app) {
        $array = ["message" => "Welcome to Sacred Heart Monastery REST Service!",  "success" => true, "code" => 200, "time" => \Carbon\Carbon::now()];
        return response()->json($array);
    });

    $app->get('get_by_email', 'AuthController@findByEmail');
    $app->post('forgot_update', 'AuthController@resetPassword');

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
            $app->post('display_name', 'AuthController@putDisplayName');
            $app->post('password', 'AuthController@putPassword');
            $app->post('latest', 'AuthController@getLatest');
            $app->post('history', 'AuthController@getHistory');
        });

        /**
         * Diocese Route
         */
        $app->post('dioceses/fetch_all', 'DioceseController@listDiocese');

        /**
         * Province route
         */
        $app->group(['prefix' => 'provinces'], function() use($app) {
            $app->post('fetch_all', 'ProvinceController@getListProvince');
        });

        /**
         * District route
         */
        $app->group(['prefix' => 'districts'], function() use($app) {
            $app->post('fetch_all', 'DistrictController@getListDistrict');
            $app->post('get_by_province', 'DistrictController@getByProvince');
            $app->post('single', 'DistrictController@getSingleDistrict');
        });

        /**
         * Member route
         */
        $app->group(['prefix' => 'members'], function () use ($app) {
            $app->post('delete', 'MemberController@deleteMember');
            $app->post('fetch_all', 'MemberController@getMembersWithPagination');
            $app->post('search', 'MemberController@search');
            $app->post('create', 'MemberController@addMember');
            $app->post('update', 'MemberController@updateMember');
            $app->post('get_charge_history', 'StatisticController@getContributeByPerson');
            $app->post('count', 'MemberController@getTotalMembersAvailable');
            $app->get('get_all', 'MemberController@getAllMembers');

        });

        /**
         * Contribute route
         */
        $app->post('contribute/charge', 'MemberController@contribute');

        /**
         * Parish Route
         */
        $app->group(['prefix' => 'parishs'], function() use($app) {
            $app->post('fetch_all', 'ParishController@listParish');
            $app->post('create', 'ParishController@createParish');
            $app->post('update', 'ParishController@updateParish');
            $app->post('remove', 'ParishController@removeParish');
            $app->post('remove_all', 'ParishController@removeAllParish');
            $app->get('get_all', 'ParishController@getAll');
        });

	    /**
	     * Sub-Parish Route
	     */
	    $app->group(['prefix' => 'subparishs'], function () use ($app) {
	    	$app->post('fetch_all', 'SubparishController@getAlls');
	    	$app->post('fetch_collection', 'SubparishController@getWithPagination');
		    $app->post('add', 'SubparishController@add');
		    $app->post('update', 'SubparishController@update');
			$app->post('remove', 'SubparishController@remove');
	    });

        /**
         * Statistic Route
         */
        $app->group(['prefix' => 'statistic'], function() use($app) {
            $app->post('overview', 'StatisticController@getOverview');

            $app->group(['prefix' => 'members'], function() use($app) {
                $app->post('get_by_parish', 'MemberController@getMemberByParish');
                $app->post('get_by_district', 'MemberController@getMemberByDistrict');
                $app->post('get_by_gender', 'MemberController@getMemberByGender');
                $app->post('get_by_diocese', 'MemberController@getMemberByDiocese');
                $app->post('get_by_province', 'MemberController@getMemberByProvince');
                $app->post('filter_by', 'MemberController@findByCondition');
            });

            $app->group(['prefix' => 'contribute'], function() use($app) {
                $app->post('by_time_range', 'StatisticController@getByTimeRange');
                $app->post('by_year', 'StatisticController@getByYear');
                $app->post('by_month_and_year', 'StatisticController@getByMonthYear');
            });
        });
    });
});