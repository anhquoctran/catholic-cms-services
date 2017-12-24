<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;

/**
 * Class ExampleController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Authenticate
     *
     * @param Request $request
     * @internal param string $username
     * @internal param string $password
     *
     * @return bool
     */
    public function postLogin(Request $request)
    {
        $errorMessages = [
            'username.required' => trans('validation.required', ['field' => trans('messages.username')]),
            'password.required' => trans('validation.required', ['field' => trans('messages.password')]),
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = User::where('username', $request->input('username'))->first();

        if (empty($user)) {
            return $this->failResponse(Response::HTTP_BAD_REQUEST, [trans('messages.login_not_found_data')]);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return $this->failResponse(Response::HTTP_BAD_REQUEST, [trans('messages.login_not_found_data')]);
        }

        /**
         * If user login success then set access_token
         */
        $user->access_token = str_random(64) . md5($user->id);
        $user->save();

        $loginHistoryData = [
            'uid' => $user->id,
            'datetime_access' => Carbon::now()->toDateTimeString(),
            'ip' => $request->input('ip'),
            'location' => $request->input('location'),
            'mac' => $request->input('mac')
        ];

        LoginHistory::create($loginHistoryData);

        return $this->succeedResponse($user);
    }

    /**
     * Logout
     *
     * @return bool
     */
    public function postLogout()
    {
        return $this->succeedResponse();
    }

    /**
     * Edit display name
     *
     * @param Request $request
     *
     * @return bool
     */
    public function putDisplayName(Request $request){
        $errorMessages = [
            'display_name.required' => trans('validation.required', ['field' => trans('messages.display_name')]),
            'display_name.max' => trans('validation.max.string', ['field' => trans('messages.display_name')]),
        ];

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|max:64',
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = User::find(app('auth')->user()->id);
        $user->display_name = $request->input('display_name');
        $user->save();

        return $this->succeedResponse($user);
    }
}
