<?php

namespace App\Http\Models\business;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\common\Constant;
use App\Http\Models\table\Anjie_task_item;

class Work extends Model
{
  protected $_anjie_task_item = null;

  public function __construct()
  {
    parent::__construct();
    $this->_anjie_task_item = new Anjie_task_item();
  }

  public function getshow($current_item_id)
  {
  	$rs = $this->_anjie_task_item->getShowByItemid($current_item_id);
  	return $rs;
  }
}

