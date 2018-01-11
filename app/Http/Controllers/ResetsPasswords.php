<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 1/9/2018
 * Time: 14:29
 */

namespace App\Http\Controllers;

use App\Models\User;
use function dd;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

trait ResetsPasswords
{
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::where('email', '=', $request->input('email'))->first();

        if (empty($user)) {

        } else {
            $broker = $this->getBroker();
            dd($broker);
            $response = $this->broker($broker)->sendResetLink(['email' => $request->input('email')], function (Message $message) {
                $message->subject($this->getEmailSubject());
            });
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return $this->getSendResetLinkEmailSuccessResponse($response);
                case Password::INVALID_USER:
                default:
                    return $this->getSendResetLinkEmailFailureResponse($response);
            }
        }
    }
    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        return property_exists($this, 'subject') ? $this->subject : 'Your Password Reset Verification Code';
    }
    /**
     * Get the response for after the reset link has been successfully sent.
     *
     * @param  string  $response
     * @return mixed
     */
    protected function getSendResetLinkEmailSuccessResponse($response)
    {
        $controller = new Controller();
        return $controller->succeedResponse();
    }
    /**
     * Get the response for after the reset link could not be sent.
     *
     * @param  string  $response
     * @return mixed
     */
    protected function getSendResetLinkEmailFailureResponse($response)
    {
        $controller = new Controller();
        return $controller->failResponse(401, 'Không thể gửi mã xác thực đến email');
    }
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        return $this->reset($request);
    }
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    private function reset(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules());
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
        $broker = $this->getBroker();
        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->getResetSuccessResponse($response);
            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }
    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return mixed
     */
    protected function resetPassword(CanResetPassword $user, $password)
    {
        $user->password = \Hash::make($password);
        $user->save();
        return response()->json(['success' => true]);
    }
    /**
     * Get the response for after a successful password reset.
     *
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetSuccessResponse($response)
    {
        return response()->json(['success' => true]);
    }
    /**
     * Get the response for after a failing password reset.
     *
     * @param  Request  $request
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetFailureResponse(Request $request, $response)
    {
        return response()->json(['success' => false]);
    }
    /**
     * Get the broker to be used during password reset.
     *
     * @return string|null
     */
    public function getBroker()
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
}