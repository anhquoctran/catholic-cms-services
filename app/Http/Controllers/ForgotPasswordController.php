<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 1/9/2018
 * Time: 11:10
 */

namespace App\Http\Controllers;


class ForgotPasswordController extends Controller
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->broker = 'users';
    }
}