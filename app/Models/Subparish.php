<?php
/**
 * Created by PhpStorm.
 * User: Anh Quoc Tran
 * Date: 2/6/2018
 * Time: 21:32
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $parish
 * @property int $parish_id
 * @property mixed $name
 */
class Subparish extends Model
{
	protected $primaryKey = "id";

	protected $hidden = [];

	protected $table = "subparishtbl";

	public $timestamps = false;

	protected $fillable = ["name", "parish_id"];

	public function parish() {
		return $this->belongsTo('App\Models\Parish', 'parish_id', 'id');
	}
}