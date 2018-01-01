<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 1/1/2018
 * Time: 16:39
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function key_exists;
use Illuminate\Support\Facades\Config;
use Validator;

class CleanupController extends Controller
{
    public function resetAll() {
        $tableNames = \Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tableNames as $name) {
            if ($name == 'usertbl' || $name == 'diocesetbl' || $name == 'provincetbl' || $name == 'districttbl') {
                continue;
            }
            \DB::table($name)->truncate();
        }

        return $this->succeedResponse(null, ['All data has been cleaned successfully']);
    }

    public function resetTable(Request $request) {
        $errorMessages = [
            'entity_name.required' => trans('validation.required', ['field' => trans('messages.entity_name')]),
        ];
        $validator = Validator::make($request->all(), [
            'entity_name' => 'required|string',
        ], $errorMessages);

        if($validator->fails()) {
            return $this->notValidateResponse($validator->errors());
        }

        $entityName = $request->get('entity_name');
        $entities = Config::get('app.entities');

        if(key_exists($entityName, $entities)) {
            $table = $entities[$entityName];

            \DB::table($table)->truncate();

            return $this->succeedResponse(null, ['Cleaned entity data!']);
        } else {
            return $this->notValidateResponse(['Entity does not exists in our system!']);
        }
    }
}