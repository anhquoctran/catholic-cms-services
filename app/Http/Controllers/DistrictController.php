<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 20:12
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\District;

class DistrictController extends Controller
{
    public function getListDistrict() {
        return $this->succeedResponse(District::all());
    }

    public function getSingleDistrict(Request $request) {
        $errorMessages = [
            'district_id.required' => trans('validation.required', ['field' => trans('messages.province_id')])
        ];
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->invalidateResponse($validator->errors());
        }

        $district = District::where('id', '=', $request->input('district_id'))->get();

        return $this->succeedResponse($district);
    }

    public function getByProvince(Request $request) {
        $errorMessages = [
            'province_id.required' => trans('validation.required', ['field' => trans('messages.province_id')])
        ];
        $validator = Validator::make($request->all(), [
            'province_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->invalidateResponse($validator->errors());
        }

        $listDistrictByProvince = District::where('province_id', '=', $request->input('province_id'))->get();

        return $this->succeedResponse($listDistrictByProvince);
    }
}