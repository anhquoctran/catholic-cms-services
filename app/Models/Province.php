<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 19:41
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $district
 * @property int $id
 */
class Province extends Model
{
    protected $table = 'provincetbl';

    protected $primaryKey = 'id';

    protected $hidden = [];

    public $timestamps = false;

    protected $fillable = ['name'];

    public function district() {
        return $this->hasMany('App\Models\District', 'province_id', 'id');
    }
}