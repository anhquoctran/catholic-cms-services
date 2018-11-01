<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 2/6/2018
 * Time: 21:48
 */

namespace App\Http\Controllers;

use App\Models\Subparish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function trans;

class SubparishController extends Controller
{
	public function getAlls(Request $request) {
		$errorMessages = [
			'parish_id.required' => trans('validation.required', ['field' => trans('messages.parish_id')]),
			'parish_id.numeric' => trans('validation.numeric', ['field' => trans('messages.parish_id')])
		];
		$validator = Validator::make($request->all(), [
			'parish_id' => 'required|numeric'
		], $errorMessages);

		if($validator->fails()) {
			return $this->notValidateResponse($validator->errors());
		}

		$parish_id = $request->input('parish_id');

		$resultData = Subparish::with(['parish.diocese'])
			->where('parish_id', '=', $parish_id)
			->get();

		return $this->succeedResponse($resultData);
	}

	public function getWithPagination() {
		$subparishs = Subparish::with(['parish.diocese'])
			->paginate($this->getPaginationPerPage());
		return $this->succeedPaginationResponse($subparishs);
	}

	public function getSingle(Request $request) {
		$errorMessages = [
			'subparish_id.required' => trans('validation.required', ['field' => trans('messages.subparish_id')]),
			'subparish_id.numeric' => trans('validation.numeric', ['field' => trans('messages.subparish_id')])
		];
		$validator = Validator::make($request->all(), [
			'subparish_id' => 'required|numeric'
		], $errorMessages);

		if($validator->fails()) {
			return $this->notValidateResponse($validator->errors());
		}

		$subparish = Subparish::with('parish.diocese')
			->where('id', '=', $request->input('subparish_id'))
			->first();
		return $this->succeedResponse($subparish);

	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function add(Request $request) {
		$errorMessages = [
			'subparish_name.required' => trans('validation.required', ['field' => trans('messages.subparish_name')]),
			'parish_id.required' => trans('validation.required', ['field' => trans('messages.parish_id')]),
			'parish_id.numeric' => trans('validation.numeric', ['field'=> trans('messages.parish_id')])
		];
		$validator = Validator::make($request->all(), [
			'subparish_name' => 'required|max:64',
			'parish_id' => 'required|numeric'
		], $errorMessages);

		if($validator->fails()) {
			return $this->notValidateResponse($validator->errors());
		}

		$subparish = new Subparish();
		$subparish->name = $request->input('subparish_name');
		$subparish->parish_id = $request->input('parish_id');
		$data = [
			'name' => $request->input('subparish_name'),
			'parish_id' => $request->input('parish_id')
		];

		Subparish::create($data);

		return $this->succeedResponse();

	}

	public function update(Request $request) {
		$errorMessages = [
			'subparish_id.required' => trans('validation.required', ['field' => trans('messages.subparish_id')]),
			'subparish_id.numeric' => trans('validation.numeric', ['field' => trans('messages.subparish_id')]),
			'subparish_name.required' => trans('validation.required', ['field' => trans('messages.subparish_name')]),
			'parish_id.required' => trans('validation.required', ['field' => trans('messages.parish_id')])
		];
		$validator = Validator::make($request->all(), [
			'subparish_id' => 'required|numeric',
			'subparish_name' => 'required|max:64',
			'parish_id' => 'required|numeric'
		], $errorMessages);

		if ($validator->fails()) {
			return $this->notValidateResponse($validator->errors());
		}

		$id = $request->input('id');
		$subparish = Subparish::find($id);
		if(empty($subparish)) {
			return $this->failResponse(404, "Không tìm thấy giáo họ này");
		} else {
			$updated = $subparish->update([
				'name' => $request->input('subparish_name'),
				'parish_id' => $request->input('parish_id')
			]);

			if ($updated) {
				return $this->succeedResponse();
			} else {
				return $this->failResponse(500, 'Cập nhật giáo họ không thành công!');
			}
		}
	}

	public function remove(Request $request) {
		$errorMessages = [
			'list_id.required' => trans('validation.required', ['field' => trans('messages.list_subparish_id')]),
			'list_id.array' => trans('validation.array', ['field' => trans('messages.list_subparish_id')])
		];
		$validator = Validator::make($request->all(), [
			'list_id' => 'required|array'
		], $errorMessages);

		if ($validator->fails()) {
			return $this->notValidateResponse($validator->errors());
		}

		$deleted = \DB::table('subparishtbl')
			->whereIn('id', $request->input('list_id'))->delete();

		if ($deleted) {
			return $this->succeedResponse();
		} else {
			return $this->failResponse(500);
		}
	}
}