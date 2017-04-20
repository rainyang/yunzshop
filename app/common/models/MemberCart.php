<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:47
 */

namespace app\common\models;



use Illuminate\Database\Eloquent\SoftDeletes;

class MemberCart extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_member_cart';

    public function isOption(){
        return !empty($this->option_id);
    }
}