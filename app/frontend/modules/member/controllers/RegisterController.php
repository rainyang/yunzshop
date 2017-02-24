<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\models\MemberModel;
use app\backend\modules\system\modules\SyssetModel;
use app\frontend\modules\member\models\SubMemberModel;

class RegisterController extends BaseController
{
    private $_error = array();

    public function index()
    {
        // 1-公众号;2-小程序;3-微信app;4-pc扫码;4-手机号/app;
        $type = \YunShop::request()->type;

        switch ($type) {
            case '4':
                $this->_mobileIndex();
                break;
        }

        $oa_wetcha = MemberFactory::create('OfficeAccount');

        $info = $oa_wetcha->getUserInfo();

    echo '<pre>';print_r($info);exit;
    }

    private function _mobileIndex()
    {
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $uniacid  = \YunShop::app()->uniacid;

        //访问来自app分享
        $from = !empty(\YunShop::request()->from) ? \YunShop::request()->from : '';

        $yzShopSet = array('isreferral'=>0); //m('common')->getSysset('shop');

               //islogined;

        $app = $this->getAppSet();

        if (!(\YunShop::app()->isajax) && !(\YunShop::app()->ispost) && $this->_validate()) {

            $member_info = MemberModel::getId($uniacid, $mobile);

            if (!empty($member_info)) {
                return show_json(0, '该手机号已被注册！');
            }

            //判断APP,PC是否开启推荐码功能
            if (is_app()) {
                $isreferral = $app['accept'];
            } else {
                $isreferral = $yzShopSet['isreferral'];
            }

            if ($isreferral == 1 && !empty(\YunShop::request()->referral)) {
                $referral = SubMemberModel::getInfo($uniacid, \YunShop::request()->referral);

                if (!$referral) {
                    return show_json(0, '推荐码无效！');
                } else {
                    $isreferraltrue = true;
                }
            }

            $member = new MemberModel();
            $member->uniacid = $uniacid;
            $member->openid  = 'u'.md5($mobile);
            $member->mobile  = $mobile;
            $member->password  = md5($password);
            $member->nickname = $mobile;
            $member->avatar = "http://".$_SERVER ['HTTP_HOST']. '/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg';

            $member->save();

            //使用推荐码 SH20160520172508468878
            if ($isreferraltrue) {
                if (!$member_info['agentid']) {
                    $m_data = array(
                        'agentid' => $referral['id'],
                        'agenttime' => time(),
                        'status' => 1,
                        'isagent' => 1
                    );
                    if($referral['member_id'] != 0){
                        //todo //p('commission')->model->upgradeLevelByAgent($referral['id']);
                    }

                    $referral->agentid = $referral['id'];
                    $referral->agenttime = time();
                    $referral->status = 1;
                    $referral->isagent = 1;

                    $referral->save();

                    $yzShopSet = m('common')->getSysset('shop');
                     //todo   //m('member')->responseReferral($yzShopSet, $referral, $member);
                }
            }

            $lifeTime = 24 * 3600 * 3;
            session_set_cookie_params($lifeTime);
            @session_start();
            $cookieid = "__cookie_sz_yi_userid_{$uniacid}";
            setcookie('member_mobile', $mobile);
            setcookie($cookieid, base64_encode($member->openid));
            if(empty($preUrl))
            {
                $preUrl =Url::app('shop.index');
            }

            if ($from == 'app') {
                $preUrl = Url::app('shop.download');
            }

            return show_json(1, $preUrl);
        }
    }

    public function bindMobile()
    {}

    private function _validate()
    {

        return !$this->_error;
    }

    private function getAppSet()
    {
        //获取APP参数设置
        if (is_app()) {
            $setdata = SyssetModel::getSysInfo(\YunShop::app()->uniacid);
            $set     = unserialize($setdata['sets']);

            return $set['app']['base'];
        }
    }
}