<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/27/2017
 * Time: 21:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'membertbl';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $hidden = [''];

    protected $fillable = [
        'uuid',
        'saint_name',
        'full_name',
        'full_name_en',
        'birth_year',
        'gender',
        'saint_name_of_relativer',
        'full_name_of_relativer',
        'birth_year_of_relativer',
        'gender_of_relativer',
        'parish_id',
        'balance',
        'phone_number',
        'date_join',
        'is_deleted',
        'is_dead',
        'description',
        'image_url',
        'district_id',
        'is_inherited'
    ];

    public function parish() {
        return $this->hasOne('App\Models\Parish', 'id', 'parish_id');
    }

    public function district() {
        return $this->hasOne('App\Models\District', 'id', 'district_id');
    }
}