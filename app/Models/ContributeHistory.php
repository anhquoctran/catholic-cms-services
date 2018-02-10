<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 12/30/2017
 * Time: 20:22
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $member
 * @property mixed $secretary
 */
class ContributeHistory extends Model
{
    protected $table = 'contributehistorytbl';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'balance',
        'datetime_charge',
        'type_charge',
        'token',
        'id_secretary',
	    'note'
    ];

    protected $hidden = ['token'];

    public $timestamps = false;

    public function member() {
        return $this->hasOne('App\Models\Member', 'id', 'member_id');
    }

    public function secretary() {
        return $this->hasOne('App\Models\User', 'id', 'id_secretary');
    }
}