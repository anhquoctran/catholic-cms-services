<?php

namespace App\Http\Controllers;

use App\Models\Diocese;
use App\Models\Parish;
use Illuminate\Http\Request;
use Validator;

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

    /**
     * Create parish
     *
     * @param Request $request
     * @internal param name
     * @internal param diocese_id
     *
     * @return bool
     */
    public function createParish(Request $request)
    {
        $errorMessages = [
            'name.required' => trans('validation.required', ['field' => trans('messages.name')]),
            'name.unique' => trans('validation.unique', ['field' => trans('messages.name')]),
            'diocese_id.required' => trans('validation.required', ['field' => 'diocese_id']),
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:parishtbl,name',
            'diocese_id' => 'required'
        ], $errorMessages);

        $diocese = Diocese::where([
            ['id', $request->input('diocese_id')],
            ['is_deleted', '<>', IS_DELETED],
        ])->get();

        $err = $validator->errors()->toArray();
        if (empty($diocese->toArray()) && empty($err['diocese_id'])) {
            $err['diocese_id'] = [trans('validation.exists_db', ['field' => 'diocese_id'])];
        }

        if (!empty($err)) {
            return $this->notValidateResponse($err);
        }

        $inputs = $request->all();
        $parish = Parish::create($inputs);

        return $this->succeedResponse(null);
    }
}
