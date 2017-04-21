<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;
use app\common\models\AccountWechats;
use app\common\models\MemberCoupon;
use app\common\models\McMappingFans;
use app\common\models\Member;
use app\common\models\Coupon;
use EasyWeChat\Foundation\Application;
use app\common\models\CouponLog;
use app\common\helpers\Url;


class SendCouponController extends BaseController
{
    const BY_MEMBERIDS = 1;
    const BY_MEMBER_LEVEL = 2;
    const BY_MEMBER_GROUP = 3;
    const TO_ALL_MEMBERS = 4;
    const TEMPLATEID = 'OPENTM200605630'; //成功发放优惠券时, 发送的模板消息的 ID

    public $couponId;
    public $failedSend = []; //发送失败时的记录
    public $adminId; //后台操作者的ID

    public function index()
    {
        $this->couponId = \YunShop::request()->id;
        $couponModel = Coupon::getCouponById($this->couponId);
        $couponResponse = [
            'resp_title' => Coupon::getter($this->couponId, 'resp_title'),
            'resp_thumb' => Coupon::getter($this->couponId, 'resp_thumb'),
            'resp_desc' => Coupon::getter($this->couponId, 'resp_desc'),
            'resp_url' => Coupon::getter($this->couponId, 'resp_url'),
        ];

        //获取会员等级列表
        $memberLevels = MemberLevel::getMemberLevelList();

        //获取会员分组列表
        $memberGroups = MemberGroup::getMemberGroupList();

        if($_POST) {

            //获取后台操作者的ID
            $this->adminId = \YunShop::app()->uid;

            //获取会员 Member ID
            $sendType = \YunShop::request()->sendtype;
            switch ($sendType) {
                case self::BY_MEMBERIDS:
                    $membersScope = \YunShop::request()->send_memberid; // todo 前端 JS 也需要检测是否符合格式
                    $patternMatch = preg_match('/(\d+,+)+(\d+,?)?/', $membersScope);
                    if (!$patternMatch) {
                        $this->error('Member ID 填写的不正确, 请重新设置');
                    }
                    $memberIds = explode(',', $membersScope);
                    break;
                case self::BY_MEMBER_LEVEL: //根据"会员等级"获取 Member IDs
                    $sendLevel = \YunShop::request()->send_level;
                    $res = MemberLevel::getMembersByLevel($sendLevel)->toArray();//todo 当没有值时, toArray()没有对象,报错
                    $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                    break;
                case self::BY_MEMBER_GROUP: //根据"会员分组"获取 Member IDs
                    $sendGroup = \YunShop::request()->send_group;
                    $res = MemberGroup::getMembersByGroupId($sendGroup)->toArray(); //todo 当没有值时, toArray()没有对象,报错
                    $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                    break;
                case self::TO_ALL_MEMBERS:
                    $members = Member::getMembersId()->toArray();
                    $memberIds = array_column($members, 'uid');
                    //$memberOpenids = array_column(array_column($members, 'has_one_fans'), 'openid');
                    break;
                default:
                    $memberIds = '';
            }

            if (empty($memberIds)) {
                $this->error('该类别下没有用户');
            }

            //更新优惠券的推送设置
            $couponResponse = \YunShop::request()->couponresponse; //优惠券的推送设置
            $couponModel->update($couponResponse);

            //获取发放的数量
            $sendTotal = \YunShop::request()->send_total;
            if($sendTotal < 1){
                $this->error('发放的数量不能小于 1');
            } else {
                //发放优惠券
                $res = $this->sendCoupon($memberIds, $sendTotal, $couponResponse);
                if ($res){
                    return $this->message('手动发送优惠券成功');
                } else{
                    return $this->message('有部分优惠券未能发送, 请检查数据库','','error');
                }
            }
        }

        return view('coupon.send', [
            'couponid' => \YunShop::app()->uniacid,
            'coupondec' => \YunShop::request()->coupondec,
            'send_total' => isset($sendTotal) ? $sendTotal : 0,
            'sendtype' => isset($sendType) ? $sendType : 1,
            'memberLevels' => $memberLevels, //用户等级列表 //bingo
            'memberGroups' => $memberGroups, //用户分组列表 //bingo
            'send_level' => isset($sendLevel) ? $sendLevel : 1,
            'memberGroupId' => isset($sendGroup) ? $sendGroup : 1,
            'agentLevelId' => isset($sendLevel) ? $sendLevel : 1,
            'couponresponse' => $couponResponse,
        ])->render();
    }


    //发放优惠券
    //array $members
    public function sendCoupon($memberIds, $sendTotal, $couponResponse)
    {
        //todo 后期任务: 在前台设置验证, 如果达到个人领取上限,ajax后提醒

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'coupon_id' => $this->couponId,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];

        foreach ($memberIds as $memberId) {

            //获取Openid
            $memberOpenid = McMappingFans::getFansById($memberId)->openid;

            for ($i = 0; $i < $sendTotal; $i++){
                $memberCoupon = new MemberCoupon;
                $data['uid'] = $memberId;
                $res = $memberCoupon->create($data);

                //写入log
                if ($res){ //发放优惠券成功
                    $log = '手动发放优惠券成功: 管理员( ID 为 '.$this->adminId.' )成功发放 '.$sendTotal.' 张优惠券( ID为 '.$this->couponId.' )给用户( Member ID 为 '.$memberId.' )';
                    //$this->sendTemplateMessage($memberOpenid, self::TEMPLATEID, $couponResponse); //成功时, 发送模板消息
                } else{ //发放优惠券失败
                    $log = '手动发放优惠券失败: 管理员( ID 为 '.$this->adminId.' )发放优惠券( ID为 '.$this->couponId.' )给用户( Member ID 为 '.$memberId.' )时失败!';
                    $this->failedSend[] = $log; //失败时, 记录 todo 最后需要展示出来
                }
                $this->log($log, $memberId);
            }
        }

        if(empty($this->failedSend)){
            return true;
        } else {
            return false;
        }
    }

    //写入日志
    public function log($log, $memberId)
    {
        $logData = [
            'uniacid' => \YunShop::app()->uniacid,
            'logno' => $log,
            'member_id' => $memberId,
            'couponid' => $this->couponId,
            'paystatus' => 0, //todo 手动发放的不需要支付?
            'creditstatus' => 0, //todo 手动发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $res = CouponLog::create($logData);
        return $res;
    }

    //发送模板消息
    //$resUrl 推送消息的链接
    public function sendTemplateMessage($openid, $templateid, $data)
    {
        $account      = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

        $options = [
            'app_id' => $account->key,
            'secret' => $account->secret,
            'token' => \YunShop::app()->account['token'],
        ];
        $app = new Application($options);
        $notice = $app->notice;
        $url = $data['resp_url'];

        $templateData = array(
            "first" => $data['resp_title'],
            "keyword1" => $data['resp_thumb'],
            "keyword2" => $data['resp_url'], //todo 需要选用带url的模板消息
            "remark" => $data['resp_desc'],
        );

        $result = $notice->uses($templateid)->withUrl($url)->andData($templateData)->andReceiver($openid)->send();
        $resultArray = json_decode($result, true);
        if($resultArray['errcode'] == 0){
            return $resultArray;
        } else {
            return false;
        }
    }

}
