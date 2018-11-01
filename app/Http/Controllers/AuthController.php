<?php

namespace App\Http\Controllers;

use function dd;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use function sha1;
use function tidy_get_html_ver;
use function trans;
use Validator;
use Carbon\Carbon;
use function var_dump;

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
     * @internal param string username
     * @internal param string password
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

        $user = User::where('username', $request->input('username'))
            ->orWhere('email', '=', $request->input('username'))->first();

        $token = $user->access_token;

        if(!$this->isNullOrEmptyString($token)) {
            return $this->failResponse(Response::HTTP_FORBIDDEN,[trans('messages.access_denied')]);
        }

        if (empty($user)) {
            return $this->failResponse(Response::HTTP_BAD_REQUEST, [trans('messages.login_not_found_data')]);
        }

        if (!Hash::check(base64_decode(base64_decode($request->input('password'))), $user->password)) {
            return $this->failResponse(Response::HTTP_BAD_REQUEST, [trans('messages.login_not_found_data')]);
        }

        /**
         * If user login success then set access_token
         */
        $user->access_token = str_random(64) . \hash('sha512',$user->id);
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
        $user = User::find(app('auth')->user()->id);
        $user->access_token = '';
        $user->save();

        return $this->succeedResponse();
    }

    /**
     * Edit display name
     *
     * @param Request $request
     * @internal param string new_name
     *
     * @return bool
     */
    public function putDisplayName(Request $request){
        $errorMessages = [
            'new_name.required' => trans('validation.required', ['field' => trans('messages.display_name')]),
            'new_name.max' => trans('validation.max.string', ['field' => trans('messages.display_name')]),
            'new_email.required' => trans('validation.required', ['field' => trans('messages.email')]),
            'new_email.max' => trans('validation.max.string', ['field' => trans('messages.email')]),
        ];

        $validator = Validator::make($request->all(), [
            'new_name' => 'required|max:64',
            'new_email' => 'required|max:64'
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = User::find(app('auth')->user()->id);
        $user->display_name = $request->input('new_name');
        $user->email = $request->input('email');
        $user->save();

        return $this->succeedResponse($user);
    }

    /**
     * Edit password
     *
     * @param Request $request
     * @internal param string new_password
     *
     * @return bool
     */
    public function putPassword(Request $request)
    {
        //dd($request->all());
        $errorMessages = [

            'new_pass.required' => trans('validation.required', ['field' => trans('messages.password')]),
        ];

        $validator = Validator::make($request->all(), [
            'new_pass' => 'required',
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = User::find(app('auth')->user()->id);
        $user->password = Hash::make(base64_decode(base64_decode($request->input('new_pass'))));
        $user->save();

        return $this->succeedResponse($user);
    }

    /**
     * Get login lastest
     *
     * @return bool
     */
    public function getLatest()
    {
        $currentUserId = app('auth')->user()->id;
        $latest = LoginHistory::where('uid', $currentUserId)
            ->orderBy('datetime_access', 'desc')
            ->offset(1)
            ->limit(1)->first();

        $user = User::find($currentUserId);
        $latest->user = $user;

        return $this->succeedResponse($latest);
    }

    /**
     * Get list login history
     *
     * @param Request $request
     * @internal param date from
     * @internal param date to
     * @internal param current_page
     * @internal param per_page
     *
     * @return bool
     */
    public function getHistory(Request $request)
    {
        $errorMessages = [
            'from.date_format' => trans('validation.date_format', ['field' => trans('messages.date_from')]),
            'to.date_format' => trans('validation.date_format', ['field' => trans('messages.date_to')]),
            'to.after_or_equal' => trans('validation.after_or_equal', ['from' => trans('messages.date_from'), 'to' => trans('messages.date_to')]),
        ];

        $validator = Validator::make($request->all(), [
            'from' => 'date_format:' . DATE_FORMAT . '|nullable',
            'to' => 'date_format:' . DATE_FORMAT . '|nullable|after_or_equal:from'
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $listLoginHistory = LoginHistory::select('*')
            ->where('uid', app('auth')->user()->id)
            ->orderBy('datetime_access', 'desc');

        if (!empty($request->input('from')) && !empty($request->input('to'))) {
            $listLoginHistory->where('datetime_access', '>=', date_format(date_create($request->input('from')), DATE_FORMAT))
                ->where('datetime_access', '<=', date_format(date_create($request->input('to')), DATE_TIME_END_FORMAT));
        }

        return $this->succeedPaginationResponse($listLoginHistory->paginate($this->getPaginationPerPage()));
    }

    public function findByEmail(Request $request) {

        $errorMessages = [
            'email.required' => trans('validation.required', ['field' => trans('messages.email')]),
            'email.email' => trans('validation.email', ['field' => trans('messages.email')])
        ];

        //return response()->json($request->only('email'));
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email|max:64',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $member = User::where('email', '=', $request->query('email'))->first();
        if(!empty($member)) {
            return $this->succeedResponse(['id' => $member->id, 'display_name' => $member->display_name]);
        } else {
            return $this->failResponse(404, 'Địa chỉ email đã gửi không tồn tại trong hệ thống!');
        }
    }

    public function resetPassword(Request $request) {
        $errorMessages = [
            'new_password.required' => trans('validation.required', ['field' => trans('messages.password')]),
            'uid.required' => trans('validation.required', ['field' => trans('messages.uid')]),
            'uid.numeric' => trans('validation.numeric', ['field' => trans('messages.uid')])
        ];

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|max:64',
            'uid' => 'required|numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = User::find($request->input('uid'));
        if(!empty($user)) {
            $user->password = Hash::make(base64_decode(base64_decode($request->input('new_password'))));;
            $saved = $user->save();
            if($saved) {
                return $this->succeedResponse(null, 'Cập nhật mật khẩu mới thành công!');
            }
            else {
                return $this->failResponse(400, 'Thay đổi mật khẩu thất bại!');
            }
        } else {
            return $this->failResponse(404, 'Cập nhật mật khẩu mới thất bại! Không tìm thấy người dùng này');
        }
    }
}
