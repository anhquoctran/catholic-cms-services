<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 21:26
 */

namespace App\Http\Controllers;

use function date;
use function dd;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\ContributeHistory;
use const IS_DELETED;
use function print_r;
use function response;
use function substr;
use function trans;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
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

        $user = Member::where('is_deleted', '=', 0)->whereIn('id', $request->input('member_id'))->first();

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

        $listMembers = Member::where('parish_id', '=', $request->input('parish_id'))
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMembers);
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

        $listMember = Member::with('parish.diocese')
            ->with('district.province')
            ->where('is_deleted','<>',IS_DELETED)
            ->whereHas('parish.diocese', function($query) use($request) {
                $query->where('diocese_id', '=', $request->input('diocese_id'));
            })
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMember);
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

         $listMembers = Member::with('parish.diocese')
             ->with('district.province')
             ->whereHas('district.province', function($query) use ($request){
                 $query->where('district_id', '=', $request->input('district_id'));
             })
             ->where('is_deleted','<>', IS_DELETED)
             ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMembers);
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

        $listMembers = Member::with('parish.diocese')
            ->with('district.province')
            ->whereHas('district.province', function($query) use ($request){
                $query->where('province_id', '=', $request->input('province_id'));
            })
            ->where('is_deleted','<>', IS_DELETED)
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($listMembers);
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

        return $this->succeedPaginationResponse($listMembers);
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
            'query' => 'required|string',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $result = Member::with(['district.province', 'parish.diocese'])
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('full_name_en', 'like', '%' . $request->input('query') . '%')
            ->paginate($this->getPaginationPerPage());

        return $this->succeedPaginationResponse($result);
    }

    public function findByCondition(Request $request) {

        $errorMessages = [
            'keyword.string' => trans('validation.string', ['field' => trans('messages.query')]),
            'diocese_id.numeric' => trans('validation.numeric', ['field' => trans('messages.diocese_id')]),
            'parish_id.numeric' => trans('validation.numeric', ['field' => trans('messages.parish_id')]),
            'province_id.numeric' => trans('validation.numeric', ['field' => trans('messages.province_id')]),
            'district_id.numeric' => trans('validation.numeric', ['field' => trans('messages.district_id')])
        ];
        $validator = Validator::make($request->all(), [
            'keyword' => 'nullable|string',
            'diocese_id' => 'numeric|nullable',
            'parish_id' => 'nullable|numeric',
            'province_id' => 'nullable|numeric',
            'district_id' => 'nullable|numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $sql = Member::with('parish.diocese', 'district.province')->where('is_deleted', '<>', IS_DELETED);

        if (!empty($request->input('keyword'))) {
            $keyword = $request->input('keyword');
            $sql->where('full_name', 'LIKE', "%{$keyword}%");
        }

        if (!empty($request->input('diocese_id'))) {
            $sql->whereHas('parish.diocese', function($q) use($request) {
                $q->where('diocese_id', '=', $request->input('diocese_id'));
            });
        } else if(!empty($request->input('parish_id'))) {
            $sql->where('parish_id', '=', $request->input('parish_id'));
        }

        if(!empty($request->input('province_id'))) {
            $sql->whereHas('district.province', function ($q) use($request) {
                $q->where('province_id', '=', $request->input('province_id'));
            });
        } else if(!empty($request->input('district_id'))) {
            $sql->where('district_id', '=', $request->input('district_id'));
        }

        if(!empty($request->input('gender'))) {
            $sql->where('gender', '=', $request->input('gender'));
        }

        return $this->succeedPaginationResponse(
            $sql->paginate($this->getPaginationPerPage())
        );
    }

    public function contribute(Request $request) {

        $errorMessages = [
            'balance.required' => trans('validation.required', ['field' => trans('messages.balance')]),
            'member_id.required' => trans('validation.required', ['field' => trans('messages.member_id')]),
            'datetime_charge.required' => trans('validation.required', ['field' => trans('messages.datetime_charge')]),
            'type_charge.required' => trans('validation.required', ['field' => trans('messages.type_charge')]),
        ];
        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric',
            'member_id' => 'required|numeric',
            'datetime_charge' => 'required|date_format:Y-m-d H:i:s',
            'type_charge' => 'required|numeric|between:0,1'
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

    private static function getNextUuid() {
        $lastMember = Member::where('is_deleted', '<>', IS_DELETED)->orderByDesc('id')->first();
        $uuid = $lastMember->uuid;
        $uuid = substr($uuid, 2);
        $number = (int) $uuid;
        $number++;
        $nextUuid = 'HV'.sprintf('%05d', $number);
        return $nextUuid;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addMember(Request $request) {
        $errorMessages = [
            'full_name.required' => trans('validation.required', ['field' => trans('messages.full_name')]),
            'full_name_en.required' => trans('validation.required', ['field' => trans('messages.full_name_en')]),
            'birth_year.numeric' => trans('validation.numeric', ['field' => trans('messages.birth_year')]),
            'saint_name.string' => trans('validation.string', ['field' => trans('messages.saint_name')]),
            'gender.numeric' => trans('validation.numeric', ['field' => trans('messages.gender')]),
            'saint_name_of_relativer.string' => trans('validation.string', ['field' => trans('messages.saint_name_relativer')]),
            'full_name_of_relativer.string' =>trans('validation.string', ['field' => trans('messages.full_name_of_relativer')]),
            'birth_year_of_relativer.string' => trans('validation.string', ['field' => trans('messages.birth_year_relativer')]),
            'gender_of_relativer.numeric' => trans('validation.numeric', ['field' => trans('messages.gender_relativer')]),
            'parish_id.numeric' =>trans('validation.numeric', ['field' => trans('messages.parish_id')]),
            'phone_number.string' => trans('validation.string', ['field' => trans('messages.phone')]),
            'date_join.date' => trans('validation.date', ['field' => trans('messages.date_join')]),
            'image_url.string' => trans('validation.string', ['field' => trans('messages.image_url')]),
            'district_id.numeric' => trans('validation.numeric', ['field' => trans('messages.district_id')])
        ];
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'full_name_en' => 'required|string',
            'saint_name' => 'required|string',
            'gender' => 'nullable|numeric',
            'birth_year' => 'nullable|numeric',
            'saint_name_of_relativer' => 'nullable|string',
            'full_name_of_relativer' => 'nullable|string',
            'birth_year_of_relativer' => 'nullable|numeric',
            'gender_of_relativer' => 'nullable|numeric',
            'parish_id' => 'nullable|numeric',
            'phone_number' => 'nullable|numeric',
            'date_join' => 'nullable|date_format:Y-m-d H:i:s',
            'image_url' => 'nullable|string',
            'district_id' => 'nullable|numeric'

        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $memberData = [
            'uuid' => self::getNextUuid(),
            'full_name' => $request->input('full_name'),
            'full_name_en' => $request->input('full_name_en'),
            'saint_name' => $request->input('saint_name'),
            'gender' => empty($request->input('gender')) ? 1 : $request->input('gender') ,
            'birth_year' => empty($request->input('birth_year')) ? 1970 : $request->input('birth_year'),
            'saint_name_of_relativer' => empty($request->input('saint_name_of_relativer')) ? '' : $request->input('saint_name_of_relativer'),
            'full_name_of_relativer' => empty($request->input('full_name_of_relativer')) ? '' : $request->input('full_name_of_relativer'),
            'birth_year_of_relativer' => empty($request->input('birth_year_of_relativer')) ? '' : $request->input('birth_year_of_relativer'),
            'gender_of_relativer' => empty($request->input('gender_of_relativer')) ? '' : $request->input('gender_of_relativer'),
            'parish_id' => empty($request->input('parish_id')) ? 1 : $request->input('parish_id'),
            'phone_number' => empty($request->input('phone_number')) ? '' : $request->input('phone_number'),
            'date_join' => empty($request->input('date_join')) ? date("Y-m-d H:i:s") : $request->input('date_join') ,
            'image_url' => empty($request->input('image_url')) ? '' : $request->input('image_url'),
            'district_id' => empty($request->input('district_id')) ? 1 : $request->input('district_id')
        ];

        Member::create($memberData);
        return $this->succeedResponse(null, 'Thêm người dùng thành công!');

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateMember(Request $request) {
        $errorMessages = [
            'member_id.required' => trans('validation.required', ['field' => trans('messages.member_id')]),
            'saint_name.string' => trans('validation.string', ['field'=> trans('messages.saint_name')]),
            'saint_name.required' => trans('validation.required', ['field'=> trans('messages.saint_name')]),
            'full_name.required' => trans('validation.required', ['field' => trans('messages.full_name')]),
            'full_name_en.required' => trans('validation.required', ['field', trans('messages.full_name_en')]),
            'birth_year.numeric' => trans('validation.numeric', ['field'=> trans('messages.birth_year')]),
            'gender.numeric' => trans('validation.numeric', ['field'=> trans('messages.gender')]),
            'saint_name_of_relativer.string' => trans('validation.string', ['field' => trans('messages.saint_name_relativer')]),
            'full_name_of_relativer.string' =>trans('validation.string', ['field'=> trans('messages.full_name_of_relativer')]),
            'birth_year_of_relativer.numeric' => trans('validation.numeric', ['field'=> trans('messages.birth_year_relativer')]),
            'gender_of_relativer.numeric' => trans('validation.required', ['field'=> trans('messages.gender_relativer')]),
            'parish_id.numeric' =>trans('validation.numeric', ['field' => trans('messages.parish_id')]),
            'phone_number.string' => trans('validation.string', ['field' => trans('messages.phone')]),
            'date_join.date_format' => trans('validation.date_format', ['field' => trans('messages.date_join')]),
            'district_id.numeric' => trans('validation.numeric', ['field'=> trans('messages.district_id')]),
            'is_dead.boolean' => trans('validation.boolean', ['field' => trans('messages.is_dead')]),
            'is_inherited.boolean' => trans('validation.boolean', ['field'=> trans('messages.is_inherited')])
        ];

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|numeric',
            'full_name' => 'required|string',
            'full_name_en' => 'required|string',
            'saint_name' => 'required|string',
            'gender' => 'nullable|numeric',
            'birth_year' => 'nullable|numeric',
            'saint_name_of_relativer' => 'nullable|string',
            'full_name_of_relativer' => 'nullable|string',
            'birth_year_of_relativer' => 'nullable|numeric',
            'gender_of_relativer' => 'nullable|numeric',
            'parish_id' => 'nullable|numeric',
            'phone_number' => 'nullable|numeric',
            'image_url' => 'nullable',
            'date_join' => 'nullable|date_format:Y-m-d H:i:s',
            'district_id' => 'nullable|numeric',
            'is_dead' => 'nullable|boolean',
            'is_inherited' => 'nullable|boolean'

        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $memberId = $request->input('member_id');

        $member = Member::find($memberId);
        $member->full_name = $request->input('full_name');
        $member->full_name_en = $request->input('full_name_en');
        $member->saint_name = $request->input('saint_name');
        $member->gender = empty($request->input('gender')) ? 1 : $request->input('gender');
        $member->birth_year = empty($request->input('birth_year')) ? 1970 : $request->input('birth_year');
        $member->saint_name_of_relativer = empty($request->input('saint_name_of_relativer')) ? '' : $request->input('saint_name_of_relativer');
        $member->full_name_of_relativer = empty($request->input('full_name_of_relativer')) ? '' : $request->input('full_name_of_relativer');
        $member->birth_year_of_relativer = empty($request->input('birth_year_of_relativer')) ? 1970 : $request->input('birth_year_of_relativer');
        $member->gender_of_relativer = empty($request->input('gender_of_relativer')) ? 1 : $request->input('gender_of_relativer');
        $member->parish_id = empty($request->input('parish_id')) ? 1 : $request->input('parish_id');
        $member->phone_number = empty($request->input('phone_number')) ? '' : $request->input('phone_number');
        $member->date_join = empty($request->input('date_join')) ? date('Y-m-d h:i:s') : $request->input('date_join');
        $member->image_url = empty($request->input('image_url')) ? '' : $request->input('image_url');
        $member->district_id = empty($request->input('district_id')) ? 1 : $request->input('district_id');
        $member->is_dead = empty($request->input('is_dead')) ? 1 : $request->input('is_dead');
        $member->is_inherited = empty($request->input('is_inherited')) ? false : $request->input('is_inherited');

        $saved = $member->save();

        if($saved) {
            return $this->succeedResponse(null, "Cập nhật thông tin hội viên thành công!");
        } else {
            return $this->notValidateResponse(['Không thể cập nhật thông tin hội viên này, vui lòng kiểm tra lại!']);
        }
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

        Member::whereIn('id', $request->input('list_member_id'))->update(['is_deleted' => IS_DELETED, 'uuid' => '']);
        ContributeHistory::whereIn('member_id', $request->input('list_member_id'))->update(['member_id' => 0]);
        return $this->succeedResponse(null);
    }
}