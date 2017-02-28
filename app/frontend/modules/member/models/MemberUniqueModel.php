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

    public static function insertData($data)
    {
        $default = array(
            'uniacid' => 0,
            'unionid' => 0,
            'member_id' => 0,
            'type' => '',
            'created_at' => time()
        );

        $data = array_merge($default, $data);

        self::insert($data);
    }

    public static function updateData($data)
    {
        self::where('unique_id', $data['unique_id'])
            ->update(array('type', $data['type']));
    }
}