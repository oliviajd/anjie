<?php

namespace App\Http\Models\table;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class CarFiles extends Model
{
    protected $table = 'car_files';
    public $timestamps = false;
    protected $primaryKey = 'id';

}