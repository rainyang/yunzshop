<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 下午6:35
 */

namespace app\frontend\modules\member\models;



class MemberFavorite extends \app\common\models\MemberFavorite
{
    /**
     * Add collection
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function createMemberFavorite($data = array())
    {
        return static::insert($data);
    }
    /**
     * remove collection
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function deleteMemberFavorite($favoriteId)
    {
        return static::where('id', $favoriteId)->delete();
    }
}
