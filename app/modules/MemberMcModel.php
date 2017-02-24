<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:53
 */

/**
 * 会员表
 */
namespace app\modules;

use app\frontend\models\Member;
use Illuminate\Database\Eloquent\Model;

class MemberMcModel extends Model
{
    public $table = 'mc_members';

    public static function getId($uniacid, $mobile)
    {
        return MemberMcModel::where('uniacid', $uniacid)->where('mobile', $mobile)->get();
    }
}