<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午10:42
 */

/**
 * 微信开放平台Unionid表
 */
namespace app\frontend\modules\member\models;

use Illuminate\Database\Eloquent\Model;

class MemberUniqueModel extends Model
{
    public $table = 'yz_member_unique';

    public static function getUnionidInfo($uniacid, $unionid)
    {
        return self::where('uncaid', $uniacid)->where('unionid', $unionid)->get();
    }
}