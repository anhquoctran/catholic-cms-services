<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/30/2017
 * Time: 20:29
 */

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Parish;
use App\Models\Diocese;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function getOverview() {
        $memberHasLargestBalance = DB::table('membertbl')
            ->where('is_deleted', '<>', IS_DELETED)
            ->where('balance', DB::raw("(select max(`balance`) from membertbl)"))
            ->first();

        $totalOfMembersAvailable = Member::where('is_deleted', '<>', IS_DELETED)
            ->distinct('id')
            ->count();

        $totalOfParishs = Parish::where('is_deleted', '<>', IS_DELETED)
            ->distinct('id')
            ->count();

        $totalOfDioceses = Diocese::where('is_deleted', '<>', IS_DELETED)
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

    }

    public function getByYear(Request $request) {

    }

    public function getByMonthYear(Request $request) {

    }
}