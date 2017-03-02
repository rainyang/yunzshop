<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:01
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

class MemberGroup extends Model
{
    protected $table = 'yz_member_group';

    protected $uniacid;

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid;
    }
}
