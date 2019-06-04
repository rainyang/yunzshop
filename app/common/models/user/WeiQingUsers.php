<?php
	
namespace app\common\models\user;

use app\common\models\BaseModel;

class WeiQingUsers extends BaseModel
{
	
    public $table = 'users';
    protected $guarded = [''];
    protected $primaryKey = 'uid';
    public $timestamps = false;

    public function __construct()
    {
        if (env('APP_Framework') == 'platform') {
            $this->table = 'yz_admin_users';
            $this->timestamps = true;
        }
    }
}