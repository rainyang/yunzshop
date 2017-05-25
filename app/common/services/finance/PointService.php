<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/10
 * Time: 下午5:49
 */

namespace app\common\services\finance;


use app\backend\modules\member\models\Member;
use app\common\models\finance\PointLog;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;

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

    protected $member;
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
        if (!isset($point_data['point'])) {
            return;
        }
        $this->point_data = $point_data;
        $member = Member::getMemberById($point_data['member_id']);
        $this->member = $member;
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

    public function addLog()
    {
        $this->point_data['uniacid'] = \YunShop::app()->uniacid;
        $point_model = PointLog::create($this->point_data);
        if (!isset($point_model)) {
            return false;
        }
        $this->messageNotice();
        return $point_model;
    }

    public function messageNotice()
    {
        $this->point_data['point_mode'] = $this->getModeAttribute($this->point_data['point_mode']);
        if (!$this->member['openid']) {
            return;
        }
        $news = new News([
            'title'       => '积分变动通知',
            'description' => '尊敬的[' . $this->member['nickname'] ? $this->member['nickname'] : $this->member['realname'] . ']，您与[' . date('Y-m-d H:i', time()) . ']发生积分变动，变动数值为[' . $this->point_data['point'] . ']，类型[' . $this->point_data['point_mode'] . ']，您目前积分余值为[' . $this->point_data['after_point'] . ']',
            'url'         => '',
            'image'       => '',
            // ...
        ]);
        PointNoticeService::sendNotice($this->member['openid'], $news);
    }

    /**
     * @name 获取变化之后的积分
     */
    public function getAfterPoint()
    {
        $this->point_data['before_point'] = $this->member_point;
        $this->member_point += $this->point_data['point'];
        if ($this->member_point < PointService::POINT) {
            $this->member_point = PointService::POINT;
        }
        $this->point_data['after_point'] = $this->member_point;
    }

    public function getModeAttribute($mode)
    {
        $mode_attribute = '';
        switch ($mode) {
            case (1):
                $mode_attribute = self::POINT_MODE_GOODS_ATTACHED;
                break;
            case (2):
                $mode_attribute = self::POINT_MODE_ORDER_ATTACHED;
                break;
            case (3):
                $mode_attribute = self::POINT_MODE_POSTER_ATTACHED;
                break;
            case (4):
                $mode_attribute = self::POINT_MODE_ARTICLE_ATTACHED;
                break;
            case (5):
                $mode_attribute = self::POINT_MODE_ADMIN_ATTACHED;
                break;
        }
        return $mode_attribute;
    }
}