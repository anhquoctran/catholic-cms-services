<?php

namespace App\Http\Controllers;

use App\Models\Parish;
use Illuminate\Http\Request;

/**
 * Class ParishController
 * @package App\Http\Controllers
 */
class ParishController extends Controller
{
    /**
     * Get list diocese
     *
     * @param Request $request
     * @internal param diocese_id
     * @internal param current_page
     * @internal param per_page
     *
     * @return bool
     */
    public function listParish(Request $request)
    {
        $parish = Parish::select(
            'parishtbl.id', 'parishtbl.name',
            'parishtbl.diocese_id', 'diocesetbl.name as diocese_name',
            'parishtbl.is_deleted'
        )->join('diocesetbl', 'parishtbl.diocese_id', '=', 'diocesetbl.id');

        if (!empty($request->input('diocese_id'))) {
            return $this->succeedResponse(
                $parish->where([
                    ['parishtbl.is_deleted', '<>', IS_DELETED],
                    ['diocesetbl.is_deleted', '<>', IS_DELETED],
                    ['parishtbl.diocese_id', $request->input('diocese_id')],
                ])->get()
            );
        } else {
            $parish->where([
                ['parishtbl.is_deleted', '<>', IS_DELETED],
                ['diocesetbl.is_deleted', '<>', IS_DELETED],
            ])->get();

            return $this->succeedPaginationResponse($parish->paginate($this->getPaginationPerPage()));
        }
    }
}
