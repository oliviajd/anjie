<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $identity
 * @property $transaction_pwd
 */

class Anjie_work_affiliated extends Model
{
    protected  $table='anjie_work_affiliated';
    public $primaryKey='id';
    public $timestamps=false;

}
