<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/5 下午4:54
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\models;


use app\common\scopes\MemberIdScope;
use Illuminate\Support\Facades\DB;

class Income extends \app\common\models\Income
{
    protected $appends = [];


    public static function boot()
    {
        parent::boot();
        self::addGlobalScope('member_id', new MemberIdScope);
    }

}
