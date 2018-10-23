<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:47
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MemberCart
 * @package app\common\models
 * @property int plugin_id
 */
class MemberCart extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_member_cart';

    public function isOption(){
        return !empty($this->option_id);
    }
    public function goods()
    {
        return $this->belongsTo(self::getNearestModel('Goods'));
    }
}