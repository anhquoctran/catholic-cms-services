<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Parish
 *
 * @package App\Models
 */
class Parish extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parishtbl';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_deleted', 'diocese_id'
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
