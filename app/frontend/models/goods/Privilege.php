<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/27
 * Time: 上午10:54
 */

namespace app\frontend\models\goods;

use app\common\exceptions\AppException;
use app\common\models\MemberShopInfo;
use app\frontend\models\goods;
use app\frontend\modules\goods\models\goods\MemberGroup;
use app\frontend\models\MemberLevel;
use app\frontend\modules\member\services\MemberService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Privilege extends \app\common\models\goods\Privilege
{
    protected $casts = [
        'time_begin_limit' => 'datetime',
        'time_end_limit' => 'datetime',
    ];

    public function validate($num)
    {
        $this->validateTimeLimit();
        $this->validateOneBuyLimit($num);
        $this->validateTotalBuyLimit($num);
        $this->validateMemberLevelLimit();
        $this->validateMemberGroupLimit();
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    /**
     * 限时购
     * @throws AppException
     */
    public function validateTimeLimit()
    {
        if ($this->enable_time_limit) {
            if (Carbon::now()->lessThan($this->time_begin_limit)) {
                throw new AppException('商品(' . $this->goods->title . ')将于' . $this->time_begin_limit->toDateTimeString() . '开启限时购买');
            }
            if (Carbon::now()->greaterThanOrEqualTo($this->time_end_limit)) {
                throw new AppException('商品(' . $this->goods->title . ')该商品已于' . $this->time_end_limit->toDateTimeString() . '结束限时购买');
            }
        }
    }

    /**
     * 用户单次购买限制
     * @param $num
     * @throws AppException
     */
    public function validateOneBuyLimit($num = 1)
    {
        if ($this->once_buy_limit > 0) {
            if ($num > $this->once_buy_limit)
                throw new AppException('商品(' . $this->goods->title . ')单次最多可购买' . $this->once_buy_limit . '件');
        }
    }

    /**
     * 用户购买总数限制
     * @param $num
     * @throws AppException
     */
    public function validateTotalBuyLimit($num = 1)
    {
        $history_num = MemberService::getCurrentMemberModel()->orderGoods()->where('goods_id', $this->goods_id)->sum('total');
        if ($this->total_buy_limit > 0) {
            if ($history_num + $num > $this->total_buy_limit)
                throw new AppException('您已购买' . $history_num . '件商品(' . $this->goods->title . '),最多可购买' . $this->total_buy_limit . '件');
        }
    }

    /**
     * 用户等级限制
     * @author shenyang
     * @throws AppException
     */
    public function validateMemberLevelLimit()
    {
        if (empty($this->buy_levels)) {
            return;
        }
        $buy_levels = explode(',', $this->buy_levels);
        $level_names = MemberLevel::select(DB::raw('group_concat(level_name) as level_name'))->whereIn('id', $buy_levels)->value('level_name');
        if (empty($level_names)) {
            return;
        }

        if (!in_array(MemberShopInfo::whereMemberId(\YunShop::app()->getMemberId())->value('level_id'), $buy_levels)) {
            throw new AppException('商品(' . $this->goods->title . ')仅限' . $level_names . '购买');
        }
    }

    /**
     * 用户组限制
     * @author shenyang
     * @throws AppException
     */
    public function validateMemberGroupLimit()
    {
        if (empty($this->buy_groups)) {
            return;
        }
        $buy_groups = explode(',', $this->buy_groups);
        $group_names = MemberGroup::select(DB::raw('group_concat(group_name) as level_name'))->whereIn('id', $buy_groups)->value('level_name');
        if (empty($group_names)) {
            return;
        }
        if (!in_array(MemberShopInfo::whereMemberId(\YunShop::app()->getMemberId())->value('group_id'), $buy_groups)) {
            throw new AppException('(' . $this->goods->title . ')该商品仅限[' . $group_names . ']购买');
        }
    }
}