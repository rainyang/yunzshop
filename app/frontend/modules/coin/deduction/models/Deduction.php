<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:14
 */

namespace app\frontend\modules\coin\deduction\models;


use app\common\models\BaseModel;
use app\common\models\VirtualCoin;

class Deduction extends BaseModel
{
    protected $table = 'yz_deduction';
    private $setting;
    // todo 初始化setting
    public function coin()
    {
        return $this->hasOne(VirtualCoin::class,'code','code');
    }
}