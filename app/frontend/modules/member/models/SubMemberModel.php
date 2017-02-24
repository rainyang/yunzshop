<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午11:00
 */

/**
 * 会员辅助表
 */
namespace app\frontend\modules\member\models;

use Illuminate\Database\Eloquent\Model;

class SubMemberModel extends Model
{
    public $table = 'yz_member';

    public static function getInfo($uniacid, $referralsn)
    {
        return self::where('uniacid', $uniacid)->where('referralsn', $referralsn)->first();
    }
}