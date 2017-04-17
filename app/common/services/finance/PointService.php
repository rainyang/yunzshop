<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/10
 * Time: 下午5:49
 */

namespace app\common\services\finance;


use app\backend\modules\member\models\Member;
use app\common\models\finance\PointLog;

class PointService
{
    const POINT_INCOME_GET = 1; //获得

    const POINT_INCOME_LOSE = -1; //失去

    const POINT_MODE_GOODS = 1; //商品赠送
    const POINT_MODE_GOODS_ATTACHED = '商品赠送';

    const POINT_MODE_ORDER = 2; //订单赠送
    const POINT_MODE_ORDER_ATTACHED = '订单赠送';

    const POINT_MODE_POSTER = 3; //超级海报
    const POINT_MODE_POSTER_ATTACHED = '超级海报';

    const POINT_MODE_ARTICLE = 4; //文章营销
    const POINT_MODE_ARTICLE_ATTACHED = '文章营销';

    const POINT_MODE_ADMIN = 5; //后台充值
    const POINT_MODE_ADMIN_ATTACHED = '后台充值';

    const POINT_MODE_BY = 6; //购物抵扣
    const POINT_MODE_BY_ATTACHED = '购物抵扣';

    const POINT = 0;

    public $point_data;

    public $member_point;
    /*
     * $data = [
     *      'point_income_type' //失去还是获得 POINT_INCOME_GET OR POINT_INCOME_LOSE
     *      'point_mode' // 1、2、3、4、5 收入方式
     *      'member_id' //会员id
     *      'point' //获得or支出多少积分
     *      //'before_point' //获取or支出 之前 x积分
     *      //'after_point' //获得or支出 之后 x积分
     *      'remark'   //备注
     * ]
     * */

    public function __construct(array $point_data)
    {
        if (!$point_data) {
            return;
        }
        $this->point_data = $point_data;
        $member = Member::getMemberById($point_data['member_id']);
        $this->member_point = $member['credit1'];
    }

    /**
     * @name 更新会员积分
     * @return PointLog point_model
     */
    public function changePoint()
    {
        $this->getAfterPoint();
        Member::updateMemberInfoById(['credit1' => $this->member_point], $this->point_data['member_id']);
        return $this->addLog();
    }

    /**
     * @name 添加log
     * @return PointLog point_model
     */
    public function addLog()
    {
        $this->point_data['uniacid'] = \YunShop::app()->uniacid;
        return PointLog::create($this->point_data);
    }

    /**
     * @name 获取变化之后的积分
     */
    public function getAfterPoint()
    {
        $this->point_data['before_point'] = $this->member_point;
        /*if ($this->point_data['point_income_type'] == PointService::POINT_INCOME_GET) {
            $this->member_point += $this->point_data['point'];
        } else if ($this->point_data['point_income_type'] == PointService::POINT_INCOME_LOSE) {
            $this->member_point -= $this->point_data['point'];
        }*/
        $this->member_point += $this->point_data['point'];
        if ($this->member_point < PointService::POINT) {
            $this->member_point = PointService::POINT;
        }
        $this->point_data['after_point'] = $this->member_point;
    }
}