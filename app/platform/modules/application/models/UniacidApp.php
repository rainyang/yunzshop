<?php

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UniacidApp extends BaseModel
{
	protected $table = 'uniacid_app';
	protected $search_fields = ['name', 'uniacid'];

}