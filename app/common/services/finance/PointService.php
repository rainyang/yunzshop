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
use app\common\models\notice\MessageTemp;
use app\common\services\MessageService;
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
    
    const POINT_MODE_TEAM = 7; //团队奖励
    const POINT_MODE_TEAM_ATTACHED = '团队奖励';

    const POINT_MODE_LIVE = 8; //生活缴费奖励
    const POINT_MODE_LIVE_ATTACHED = '生活缴费奖励';

    const POINT_MODE_AIR = 10; //飞机票
    const POINT_MODE_AIR_ATTACHED = '飞机票奖励';

    const POINT_MODE_CASHIER = 9; //收银台奖励
    const POINT_MODE_CASHIER_ATTACHED = '收银台奖励';

    const POINT_MODE_STORE = 93; //收银台奖励
    const POINT_MODE_STORE_ATTACHED = '门店奖励';

    const POINT_MODE_RECHARGE = 11; //话费充值奖励
    const POINT_MODE_RECHARGE_ATTACHED = '话费充值奖励';

    const POINT_MODE_FLOW = 12; //流量充值奖励
    const POINT_MODE_FlOW_ATTACHED = '流量充值奖励';

    const POINT_MODE_TRANSFER = 13; //转让
    const POINT_MODE_TRANSFER_ATTACHED = '转让-转出';

    const POINT_MODE_RECIPIENT = 14; //转让
    const POINT_MODE_RECIPIENT_ATTACHED = '转让-转入';

    const POINT_MODE_ROLLBACK = 15; //回滚
    const POINT_MODE_ROLLBACK_ATTACHED = '积分返还';

    const POINT_MODE_COUPON_DEDUCTION_AWARD = 16;
    const POINT_MODE_COUPON_DEDUCTION_AWARD_ATTACHED = '优惠劵抵扣奖励';

    const POINT_MODE_TRANSFER_LOVE = 18;
    const POINT_MODE_TRANSFER_LOVE_ATTACHED = '自动转出';

    const POINT_MODE_RECHARGE_CODE = 92;
    const POINT_MODE_RECHARGE_CODE_ATTACHED = '充值码充值积分';


    const POINT_MODE_TASK_REWARD = 17;
    const POINT_MODE_TASK_REWARD_ATTACHED = '任务奖励';

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
     * @return
     */

    public function changePoint()
    {
        $point = floor($this->point_data['point'] * 100) / 100;
        if ($this->point_data['point_income_type'] == self::POINT_INCOME_LOSE) {
            $point = floor(abs($this->point_data['point']) * 100) / 100;
        }
        if ($point < 0.01) {
            return false;
        }
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
        $noticeMember = Member::getMemberByUid($this->member->uid)->with('hasOneFans')->first();
        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('shop.notice')['point_change'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $this->member['nickname']],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '积分变动金额', 'value' => $this->point_data['point']],
            ['name' => '积分变动类型', 'value' => $this->point_data['point_mode']],
            ['name' => '变动后积分数值', 'value' => $this->point_data['after_point']]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }
        MessageService::notice(MessageTemp::$template_id, $msg, $this->member->uid);
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
            case (6):
                $mode_attribute = self::POINT_MODE_BY_ATTACHED;
                break;
            case (7):
                $mode_attribute = self::POINT_MODE_TEAM_ATTACHED;
                break;
            case (8):
                $mode_attribute = self::POINT_MODE_LIVE_ATTACHED;
                break;
            case (9):
                $mode_attribute = self::POINT_MODE_CASHIER_ATTACHED;
                break;
            case (10):
                $mode_attribute = self::POINT_MODE_AIR_ATTACHED;
                break;
            case (11):
                $mode_attribute = self::POINT_MODE_RECHARGE_ATTACHED;
                break;
            case (12):
                $mode_attribute = self::POINT_MODE_FLOW_ATTACHED;
                break;
            case (13):
                $mode_attribute = self::POINT_MODE_TRANSFER_ATTACHED;
                break;
            case (14):
                $mode_attribute = self::POINT_MODE_RECIPIENT_ATTACHED;
                break;
            case (15):
                $mode_attribute = self::POINT_MODE_ROLLBACK_ATTACHED;
                break;
            case (16):
                $mode_attribute = self::POINT_MODE_COUPON_DEDUCTION_AWARD_ATTACHED;
                break;
            case (17):
                $mode_attribute = self::POINT_MODE_TASK_REWARD_ATTACHED;
                break;
            case (18):
                $mode_attribute = self::POINT_MODE_TRANSFER_LOVE_ATTACHED;
                break;
            case (92):
                $mode_attribute = self::POINT_MODE_RECHARGE_CODE_ATTACHED;
                break;
            case (93):
                $mode_attribute = self::POINT_MODE_STORE_ATTACHED;
                break;

        }
        return $mode_attribute;
    }
}