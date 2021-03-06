<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 20:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $province
 * @property mixed $member
 */
class District extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'districttbl';

    /**
     * /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'province_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes will hidden on json return.
     *
     * @var array
     */
    protected $hidden = [''];

    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }

    public function member() {
        return $this->hasMany('App\Models\Member', 'district_id', 'id');
    }
}