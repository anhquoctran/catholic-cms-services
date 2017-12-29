<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 21:26
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Validator;

class MemberController extends Controller
{
    public function getAllMembers(Request $request) {

        $member = new Member();
        $allColumns = $member->fillable;

        $listMembers = Member::with('district')
            ->select(array_diff($allColumns, ['parish_id', 'district_id']))
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMembers);
    }


    public function getSingleMember(Request $request) {
        $errorMessages = [
            'member_id.required' => trans('validation.required', ['field' => trans('messages.member_id')])
        ];
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $user = Member::find($request->input('member_id'));

        return $this->succeedResponse($user);
    }

    public function getMemberByParish(Request $request) {

        $errorMessages = [
            'parish_id.required' => trans('validation.required', ['field' => trans('messages.parish_id')])
        ];
        $validator = Validator::make($request->all(), [
            'parish_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $listMembers = Member::where('parish_id', '=', $request->input('parish_id'))->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMembers);
    }

    public function getMemberByDiocese(Request $request) {
        $errorMessages = [
            'diocese_id.required' => trans('validation.required', ['field' => trans('messages.diocese_id')])
        ];
        $validator = Validator::make($request->all(), [
            'diocese_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $listMember = DB::table('membertbl m')->select(
            [
                'm.id',
                'm.uuid',
                'm.saint_name',
                'm.full_name',
                'm.gender',
                'm.birth_year',
                'm.district_id',
                'm.saint_name_of_relativer',
                'm.full_name_of_relativer',
                'm.gender_of_relativer',
                'm.birth_year_of_relativer',
                'm.balance',
                'm.phone_number',
                'm.date_join',
                'm.parish_id',
                'm.image_url',
                'm.description',
                'm.is_dead',
                'm.is_inherited'
            ]
        )->leftJoin('','','','')
            ->leftJoin('','','','')
            ->where('','','')
            ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMember);
    }

    public function getMemberByDistrict(Request $request) {

    }

    public function getMemberByProvince(Request $request) {

    }
}