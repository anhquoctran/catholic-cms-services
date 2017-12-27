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


}