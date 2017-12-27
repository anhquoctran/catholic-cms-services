<?php

namespace App\Http\Controllers;

use App\Models\Diocese;

/**
 * Class DioceseController
 * @package App\Http\Controllers
 */
class DioceseController extends Controller
{
    /**
     * Get list diocese
     *
     * @return bool
     */
    public function listDiocese(){
        return $this->succeedResponse(Diocese::where('is_deleted', '<>', IS_DELETED)->get());
    }
}
