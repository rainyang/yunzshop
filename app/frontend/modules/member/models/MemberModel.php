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
namespace app\frontend\modules\member\models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

class MemberModel extends Model
{
    public $table = 'mc_members';

    protected $guarded = ['credit1', 'credit2', 'credit3', 'credit4' , 'credit5'];

    protected $fillable = ['email'=>'xxx@xx.com'];

    public static function getId($uniacid, $mobile)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->get()
            ->toArray();
    }


    public static function insertData($data)
    {
        return self::insertGetId($data);
    }

}