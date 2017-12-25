<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoginHistory
 *
 * @package App\Models
 */
class LoginHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loginhistorytbl';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'datetime_access', 'ip', 'location', 'uid', 'mac'
    ];

    /**
     * The attributes will hidden on json return.
     *
     * @var array
     */
    protected $hidden = [''];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
