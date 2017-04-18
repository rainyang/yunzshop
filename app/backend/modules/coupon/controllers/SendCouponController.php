<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;
use app\common\models\Coupon;
use EasyWeChat\Foundation\Application;
use app\common\models\CouponLog;


class SendCouponController extends BaseController
{
    const BY_MEMBERIDS = 1;
    const BY_MEMBER_LEVEL = 2;
    const BY_MEMBER_GROUP = 3;
    const BY_AGENT_LEVEL = 4;
    const TO_ALL_MEMBERS = 5;

    public $uniacid;
    public $couponId;
    public $failedSend = [];

    public function temp()
    {
        $configs = \Config::get('coupon');
//        dd($configs);
        foreach ($configs as $key => $config) {
            $leveldata[$key]['name'] = $key;
            $leveldata[$key]['data'] = call_user_func([new $config['list']['class'], $config['list']['function']])->toArray();
//            dd($leveldata[$key]);
            $members = call_user_func([new $config['member']['class'], $config['member']['function']],2);
            dd($leveldata[$key]['data']);
        }


        $memberIds = array_column($members->toArray(), 'member_id');
        dd($memberIds);
        print_r ($memberIds); exit;

    }

    public function index()
    {
//        dd(\YunShop::request());
        $this->uniaicd = \YunShop::app()->uniacid;
        $this->couponId = \YunShop::request()->coupon_id;
        $configs = \Config::get('coupon');

        //获取会员等级
        $memberLevels = MemberLevel::getMemberLevelList();

        //获取会员分组列表
        $memberGroups = MemberGroup::getMemberGroupList();

        //获取分销商等级列表
        if($configs){
            foreach ($configs as $key => $config) {
                $lists[$key] = call_user_func([new $config['list']['class'], $config['list']['function']])->toArray();
            }
        }

        //获取表单提交的会员 Member ID todo JS 检测是否符合格式
        $sendType = \YunShop::request()->sendtype;
        switch ($sendType){
            case self::BY_MEMBERIDS:
                $membersScope = \YunShop::request()->send_memberid;
                $patternMatch = preg_match('/(\d+,+)+(\d+,?)?/', $membersScope);
                if(!$patternMatch){
                    $this->error('Member ID 填写的不正确, 请重新设置');
                }
                $memberIds = explode(',', $membersScope);
                break;
            case self::BY_MEMBER_LEVEL: //根据"会员等级"获取 Member IDs
                $sendLevel = \YunShop::request()->send_level;
                $res = MemberLevel::getMembersbyLevel($sendLevel)->toArray();//todo 当没有值时, toArray()没有对象,报错
                $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                break;
            case self::BY_MEMBER_GROUP: //根据"会员分组"获取 Member IDs
                $sendGroup = \YunShop::request()->send_group;
                $res = MemberGroup::getMembersByGroupId($sendGroup)->toArray(); //todo 当没有值时, toArray()没有对象,报错
                $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                break;
            default:
                $members = '';
        }

        根据"configs"中的设置获取 Member ID
        if(!empty($configs)){
            foreach ($configs as $key => $config) {
                $leveldata[$key]['name'] = $key;
                $leveldata[$key]['data'] = call_user_func([new $config['list']['class'], $config['list']['function']])->toArray();
                $members = call_user_func([new $config['member']['class'], $config['member']['function']],2);
            }
            $memberIds = array_column($members->toArray(), 'member_id');
        }

        if(empty($members)){
            $this->error('该类别下没有用户');
        }

        //获取couponId, 更新优惠券的推送设置
        $couponId = \YunShop::request()->couponid;
        $couponResponse = \YunShop::request()->couponresponse; //优惠券的推送设置
        $couponModel = Coupon::getCouponById($couponId);
//        dd($couponResponse); //应该是数组
        $couponModel->update($couponResponse); //todo update()方法不行?

        //获取目标的memberId

        //获取发放的数量 (不小于1)
        $sendTotal = \YunShop::request()->send_total;


        //获取操作员的ID
        $adminId = \YunShop::app()->uid;

        //记录到 log
        $log = [
            'success' => '管理员 '.$adminId.' 发放优惠券( ID为 '.$this->couponId.' )给用户 Member ID 为: ', //发放优惠券失败时的日志
            'failed' => '', //发放优惠券失败时的日志
        ];

        return view('coupon.send', [
            'configs' => $configs,
            'couponid' => $this->couponid,
            'coupondec' => \YunShop::request()->coupondec,
            'send_total' => $sendTotal,
            'sendtype' => $sendType,
            'memberLevels' => $memberLevels, //用户等级列表 //bingo
            'memberGroups' => $memberGroups, //用户分组列表 //bingo
            'agentLevels' => $agentLevels,  //分销商列表
            'send_level' => $sendLevel,
            'memberGroupId' => $sendGroup,
            'agentLevelId' => $sendLevel,
            'couponresponse' => $couponResponse,
            'lists' => $lists,
        ])->render();
    }


    //发放优惠券
    //需要提供$couponId
    //array $members
    public function sendCoupon($couponId, $members, $adminId, $sendTotal, $couponResponse)
    {
        //是否要考虑个人领取上限 todo
//        $count = MemberCoupon::getMemberCouponCount($memberId, $couponId);
//        $couponMaxLimit = Coupon::getter($couponId, 'get_max'); //优惠券的限制每人的领取总数
//        if($count >= $couponMaxLimit){
//            return $this->errorJson('该用户已经达到个人领取上限','');
//        }

        $data = [
            'uniacid' => $this->uniacid,
            'coupon_id' => $couponId,
            'get_type' => 0,
            'get_time' => strtotime('now'),
        ];
        foreach ($members as $member) {
            $data['uid'] = $member['id'];

            //需要获取member的Openid

            $memberCoupon = new MemberCoupon;
            $res = $memberCoupon->create($data);

            //写入log
            if ($res){ //发放优惠券成功
                $log = '管理员( ID 为 '.$adminId.' )成功发放 '.$sendTotal.' 张优惠券( ID为 '.$this->couponId.' )给用户 (Member ID 为 '.$member['id'].' )';
            } else{ //发放优惠券失败
                $log = '管理员( ID 为 '.$adminId.' )成功发放 '.$sendTotal.' 张优惠券( ID为 '.$this->couponId.' )给用户 (Member ID 为 '.$member['id'].' )';
                $this->failedSend[] = $log; //todo 最后展示出来
            }
            $this->log($log, $member['id']);

            //发送模板消息
            $templateid = 'OPENTM200605630';
            $this->sendTemplateMessage($member['openid'], $templateid, $couponResponse);
        }
    }

    //写入日志
    public function log($log, $memberId)
    {
        $logData = [
            'uniacid' => $this->uniacid,
            'member_id' => $memberId,
            'couponid' => $this->couponId,
            'paystatus' => 0, //todo 手动发放的不需要支付?
            'creditstatus' => 0, //todo 手动发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $couponLog = new CouponLog();
        $res = $couponLog->create($logData);
        return $res;
    }

    //发送模板消息
    //$resUrl 推送消息的链接
    public function sendTemplateMessage($openid, $templateid, $couponResponse)
    {
        $pay = \Setting::get('shop.pay');
        $options = [
            'app_id' => $pay['weixin_appid'],
            'secret' => $pay['weixin_secret'],
            'token' => \YunShop::app()->account['token'],
        ];
        $app = new Application($options);
        $notice = $app->notice;

        $templateid = 'OPENTM200605630'; //todo 改成传入的, 而不是声明的
        $data = array(
            "first" => $couponResponse['resp_title'],
            "keyword1" => $couponResponse['resp_thumb'],
            "keyword2" => $couponResponse['resp_url'], //todo 需要选用带url的模板消息
            "remark" => $couponResponse['resp_desc'],
        );

        $result = $notice->uses($templateid)->withUrl($resUrl)->andData($data)->andReceiver($openid)->send();
        $resultArray = json_decode($result, true);
        if($resultArray['errcode'] == 0){
            return $resultArray;
        } else {
            return false;
        }
    }

}
