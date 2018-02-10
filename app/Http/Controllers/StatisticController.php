<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/30/2017
 * Time: 20:29
 */

namespace App\Http\Controllers;

use App\Models\ContributeHistory;
use App\Models\Member;
use App\Models\Parish;
use App\Models\Diocese;
use const ASC;
use const DATE_TIME_FORMAT;
use const DESC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use const IS_DELETED;
use function print_r;
use function trans;
use Illuminate\Support\Facades\Validator;

class StatisticController extends Controller
{
    public function getOverview() {
        $memberHasLargestBalance = DB::table('membertbl')
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('balance', DB::raw("(select max(`balance`) from membertbl where is_deleted = 0)"))
            ->first();

        $totalOfMembersAvailable = Member::where('is_deleted', '!=', IS_DELETED)
            ->distinct('id')
            ->count();

        $totalOfParishs = Parish::where('is_deleted', '!=', IS_DELETED)
            ->distinct('id')
            ->count();

        $totalOfDioceses = Diocese::where('is_deleted', '!=', IS_DELETED)
            ->distinct('id')
            ->count();

        $totalOfBalance = DB::table('membertbl')
            ->selectRaw('sum(balance) as total')
            ->where('is_deleted', '<>', IS_DELETED)
            ->first();

        $totalOfMemberHasContributed = Member::where('balance', '!=', 0)
            ->where('is_deleted', '<>', IS_DELETED)
            ->distinct('id')
            ->count();

        $response = [
            'total_of_members' => $totalOfMembersAvailable,
            'total_of_dioceses' => $totalOfDioceses,
            'total_of_parishs' => $totalOfParishs,
            'total_of_balance' => (int) $totalOfBalance->total,
            'total_of_member_has_contributed' => $totalOfMemberHasContributed,
            'member_has_largest_balance' => $memberHasLargestBalance
        ];

        return $this->succeedResponse($response);
    }

    public function getByTimeRange(Request $request) {
        $errorMessages = [
            'from.required' => trans('validation.required', ['field' => trans('messages.from')]),
            'to.required' => trans('validation.required', ['field' => trans('messages.to')]),
            'sort.required' => trans('validation.required', ['field' => trans('messages.sort')])
        ];
        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:'.DATE_TIME_FORMAT,
            'to' => 'required|date_format:'.DATE_TIME_FORMAT,
            'sort' => 'required|numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $from = $request->input('from');
        $to = $request->input('to');
        $sort = $request->input('sort');

        $histories = ContributeHistory::with(['member.district.province', 'member.parish.diocese', 'secretary'])
            ->whereBetween('datetime_charge', [$from, $to])
            ->where('member_id', '>', 0)
            ->groupBy('member_id');

        switch ($sort) {
            case ASC :
                $histories = $histories->orderBy('balance');
                break;
            case DESC:
                $histories = $histories->orderByDesc('balance');
                break;
        }

        return $this->succeedResponse($histories->get(['*', 'sum(balance) as total']));
    }

    public function getByYear(Request $request) {
        $errorMessages = [
            'year.required' => trans('validation.required', ['field' => trans('messages.from')]),
            'sort.required' => trans('validation.required', ['field' => trans('messages.sort')])
        ];
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'sort' => 'required|numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $year = $request->input('year');
        $sort = $request->input('sort');

        $histories = ContributeHistory::with(
        	[
        		'member.district.province',
		        'member.parish.diocese',
		        'secretary'
	        ]
        )
            ->whereYear('datetime_charge', '=', $year)
            ->where('member_id', '>', 0)
            ->groupBy('member_id');


		switch ($sort) {
            case ASC :
                $histories = $histories->orderBy('balance');
                break;
            case DESC:
                $histories = $histories->orderByDesc('balance');
                break;
        }
        return $this->succeedResponse($histories->get());
    }

    public function getByMonthYear(Request $request) {
        $errorMessages = [
            'year.required' => trans('validation.required', ['field' => trans('messages.from')]),
            'month.required' => trans('validation.required', ['field' => trans('messages.month')]),
            'sort.required' => trans('validation.required', ['field' => trans('messages.sort')])
        ];
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'sort' => 'required|numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $year = $request->input('year');
        $month = $request->input('month');
        $sort = $request->input('sort');

        $histories = ContributeHistory::with(['member.district.province', 'member.parish.diocese', 'secretary'])
            ->whereYear('datetime_charge', '=', $year)
            ->whereMonth('datetime_charge', '=', $month)
            ->where('member_id', '>', 0)
            ->groupBy('member_id');

        switch ($sort) {
            case ASC :
                $histories = $histories->orderBy('balance');
                break;
            case DESC:
                $histories = $histories->orderByDesc('balance');
                break;
        }

        return $this->succeedResponse($histories->get(['*', 'sum(balance) as total']));
    }

    public function getContributeByPerson(Request $request) {
        $errorMessages = [
            'member_id.required' => trans('validation.required', ['field' => trans('messages.member_id')]),
            'sort.numeric' => trans('validation.numeric', ['field' => trans('messages.sort')])
        ];
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|numeric',
            'sort' => 'numeric'
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }
        $sort = $request->input('sort');
        $histories = ContributeHistory::with(['member.parish.diocese', 'member.district.province', 'secretary'])

            ->where('member_id', '>', 0)
            ->whereHas('member', function($query) use($request) {
                $query->where('id', '=', $request->input('member_id'));
             });

        switch ($sort) {
            case ASC :
                $histories = $histories->orderBy('balance');
                break;
            case DESC:
                $histories = $histories->orderByDesc('balance');
                break;
        }

        return $this->succeedPaginationResponse($histories->paginate($this->getPaginationPerPage()));
    }
}