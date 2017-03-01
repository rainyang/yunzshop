<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 下午6:35
 */

namespace app\frontend\modules\member\models;


use Illuminate\Database\Eloquent\Model;

class MemberFavorite extends Model
{
    public $table = 'yz_member_favorite';
    /**
     * 添加收藏
     * @Author::yitian 2017-03-01 qq:751818588
     * @access public static
     *
     * @param int $groupId
     *
     * @return array
     * */
    public static function createMemberFavorite()
    {

    }

}