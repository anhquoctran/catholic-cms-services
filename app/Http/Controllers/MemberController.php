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
use App\Models\ContributeHistory;
use Validator;

class MemberController extends Controller
{
    /**
     * Get all members without conditions excepts deleted members
     * @return bool
     */
    public function getMembersWithPagination() {

        $listMembers = Member::with('parish.diocese', 'district.province')
            ->where('is_deleted', '<>', IS_DELETED)
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMembers);
    }

    public function getAllMembers() {
        $listMembers = Member::with(['parish.diocese', 'district.province'])
            ->where('is_deleted', '<>', IS_DELETED)
            ->get();

        return $this->succeedResponse($listMembers);
    }

    /**
     * Gets one member by member ID, excepts deleted member
     * @param Request $request
     * @return bool
     */
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

        $user = Member::whereIn('id', $request->input('member_id'))->first();

        return $this->succeedResponse($user);
    }

    /**
     * Gets list of members by parish, specificed by parish_id, excepts deleted members
     * @param Request $request
     * @return mixed
     */
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

    /**
     * Gets list of members by diocese, specificed by diocese_id, excepts deleted members
     * @param Request $request
     * @return mixed
     */
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

        $listMember = Member::with(['parish.diocese' => function($query) use($request) {
            $query->where('diocestbl.id', '=', $request->input('diocese_id'));
        }])
            ->with('district.province')
            ->where('is_deleted','<>',IS_DELETED)
            ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMember);
    }

    /**
     * Gets list of members by district, specificed by district_id, excepts deleted members
     * @param Request $request
     * @return mixed
     */
    public function getMemberByDistrict(Request $request) {
        $errorMessages = [
            'district_id.required' => trans('validation.required', ['field' => trans('messages.district_id')])
        ];
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

         $listMembers = Member::with('parish.dicoese')
             ->with(['district.province' => function($query) use ($request){
                 $query->where('district.id', '=', $request->input('district_id'));
             }])
             ->where('is_deleted','<>', IS_DELETED)
             ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMembers);
    }

    /**
     * Gets list of members by province, specificed by province_id, excepts deleted members
     * @param Request $request
     * @return mixed
     */
    public function getMemberByProvince(Request $request) {
        $errorMessages = [
            'province_id.required' => trans('validation.required', ['field' => trans('messages.province_id')])
        ];
        $validator = Validator::make($request->all(), [
            'province_id' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $listMembers = Member::with('parish.dicoese')
            ->with(['district.province' => function($query) use ($request){
                $query->where('district.province_id', '=', $request->input('district_id'));
            }])
            ->where('is_deleted','<>', IS_DELETED)
            ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMembers);
    }

    /**
     * Gets list of members by gender, excepts deleted members
     * @param Request $request
     * @return mixed
     */
    public function getMemberByGender(Request $request) {
        $errorMessages = [
            'gender.required' => trans('validation.required', ['field' => trans('messages.gender')])
        ];
        $validator = Validator::make($request->all(), [
            'gender' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $listMembers = Member::with(['parish.diocese', 'district.province'])
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('gender', '=', $request->input('gender'))
            ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($listMembers);
    }

    /**
     * Returns total of available members (without deleted members)
     */
    public function getTotalMembersAvailable() {
        $count = Member::selectRaw('count(id) as total')
            ->where("is_deleted", '<>', IS_DELETED)
            ->first();

        return $this->succeedResponse(['total_members' => $count['total']]);
    }

    public function getMemberHasContribute() {
        $members = Member::with(['district', 'parish'])
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('balance', '>', 0)
            ->get();

        return $this->succeedResponse($members);
    }

    public function search(Request $request) {
        $errorMessages = [
            'query.required' => trans('validation.required', ['field' => trans('messages.query')])
        ];
        $validator = Validator::make($request->all(), [
            'query' => 'required|numeric',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $result = Member::with(['district.province', 'parish.diocese'])
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('full_name_en', 'like', "'%".$request->input('query')."%'")
            ->paginate($this->getPaginationPerPage());

        return $this->succeedResponse($result);
    }

    public function contribute(Request $request) {
        $errorMessages = [
            'balance.required' => trans('validation.required', ['field' => trans('messages.balance')]),
            'member_id.required' => trans('validation.required', ['field' => trans('messages.member_id')]),
            'datetime_charge.date_format' => trans('validation.date_format', ['field', trans('messages.datetime_charge')]),
            'datetime_charge.required' => trans('validation.required', ['field', trans('messages.datetime_charge')]),
            'type_charge.required' => trans('validation.required', ['field', trans('messages')]),
        ];
        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric',
            'member_id' => 'required|numeric',
            'datetime_charge', 'required|date_format:Y-m-d H:i:s',
            'type_charge', 'required|numberic|between0,1'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }
        $member_id = $request->input('member_id');
        $member = Member::find($member_id);

        if(!is_null($member)) {
            $typeCharge = $request->input('type_charge');
            $balance = $request->input('balance');
            $currentUserId = app('auth')->user()->id;
            $datetime_charge = $request->input('datetime_charge');

            switch($typeCharge) {
                case 0:
                    $member->balance = (int)$balance;
                    break;
                case 1:
                    $member->balance += (int)$balance;
                    break;
            }

            $saved = $member->save();

            if($saved) {
                $history = new ContributeHistory();
                $history->balance = $member->balance;
                $history->id_secretary = $currentUserId;
                $history->member_id = $member_id;
                $history->datetime_charge = $datetime_charge;
                $history->token = $request->header('Authorization');
                $history->save();
                return $this->succeedResponse(null);
            }
            else {
                return $this->notValidateResponse(['Cập nhật số tiền không thành công']);
            }
        }
        else return $this->notValidateResponse(['Không tìm thấy hội viên này trên hệ thống!']);
    }

    /**
     * @param Request $request
     */
    public function addMember(Request $request) {

    }

    /**
     * @param Request $request
     */
    public function updateMember(Request $request) {

    }

    /**
     * Delete member
     *
     * @param Request $request
     * @internal param list_member_id
     *
     * @return bool
     */
    public function deleteMember(Request $request) {
        $errorMessages = [
            'list_member_id.required' => trans('validation.required', ['field' => 'list_member_id']),
            'list_member_id.array' => trans('validation.array', ['field' => 'list_member_id']),
        ];

        $validator = Validator::make($request->all(), [
            'list_member_id' => 'required|array'
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        Member::whereIn('id', $request->input('list_member_id'))->update(['is_deleted' => IS_DELETED]);

        return $this->succeedResponse(null);
    }
}