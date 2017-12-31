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
        'id_seretary'
    ];

    protected $hidden = [];

    public $timestamps = false;

    public function member() {
        return $this->hasOne('App\Models\Member', 'id', 'member_id');
    }
}