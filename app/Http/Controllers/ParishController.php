<?php

namespace App\Http\Controllers;

use App\Models\Diocese;
use App\Models\Parish;
use Illuminate\Http\Request;
use const IS_DELETED;
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

    public function getAll() {
        $listMember = Parish::with('diocese')->get();

        return $this->succeedResponse($listMember);
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
            'name.unique' => trans('validation.unique', ['field' => trans('messages.parish_name')]),
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
        Parish::create($inputs);

        return $this->succeedResponse(null);
    }

    /**
     * Update parish
     *
     * @param Request $request
     * @internal param id
     * @internal param name
     * @internal param diocese_id
     *
     * @return bool
     */
    public function updateParish(Request $request)
    {
        $errorMessages = [
            'name.required' => trans('validation.required', ['field' => trans('messages.name')]),
            'name.unique' => trans('validation.unique', ['field' => trans('messages.name')]),
            'diocese_id.required' => trans('validation.required', ['field' => 'diocese_id']),
            'id.required' => trans('validation.required', ['field' => 'id']),
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:parishtbl,name,' . $request->input('id'),
            'diocese_id' => 'required',
            'id' => 'required'
        ], $errorMessages);

        $diocese = Diocese::where([
            ['id', $request->input('diocese_id')],
            ['is_deleted', '<>', IS_DELETED],
        ])->get();

        $parish = Parish::where([
            ['id', $request->input('id')],
            ['is_deleted', '<>', IS_DELETED],
        ])->get();

        $err = $validator->errors()->toArray();
        if (empty($diocese->toArray()) && empty($err['diocese_id'])) {
            $err['diocese_id'] = [trans('validation.exists_db', ['field' => 'diocese_id'])];
        }

        if (empty($parish->toArray()) && empty($err['id'])) {
            $err['id'] = [trans('validation.exists_db', ['field' => 'id'])];
        }

        if (!empty($err)) {
            return $this->notValidateResponse($err);
        }

        $inputs = $request->all();
        Parish::where('id', $request->input('id'))->update($inputs);

        return $this->succeedResponse(null);
    }

    /**
     * Remove parish
     *
     * @param Request $request
     * @internal param list_parish_id
     *
     * @return bool
     */
    public function removeParish(Request $request)
    {
        $errorMessages = [
            'list_parish_id.required' => trans('validation.required', ['field' => 'list_parish_id']),
            'list_parish_id.array' => trans('validation.array', ['field' => 'list_parish_id']),
        ];

        $validator = Validator::make($request->all(), [
            'list_parish_id' => 'required|array'
        ], $errorMessages);

        if ($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        Parish::whereIn('id', $request->input('list_parish_id'))->update([
            'is_deleted' => IS_DELETED,
            'name' => ''
        ]);

        return $this->succeedResponse(null);
    }

    public function removeAllParish()
    {
        Parish::truncate();

        return $this->succeedResponse(null);
    }
}
