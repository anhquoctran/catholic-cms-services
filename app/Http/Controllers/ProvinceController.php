<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 19:39
 */

namespace App\Http\Controllers;

use App\Models\Province;

class ProvinceController extends Controller
{
    public function getListProvince() {
        return $this->succeedResponse(Province::all());
    }
}