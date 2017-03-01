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
use Illuminate\Support\Str;

class RegisterController extends BaseController
{
    private $_error = array();
    private $mobile;
    private $password;
    private $uniacid;

    public function index()
    {
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> $_SESSION['member_id']));
        }

        $type = \YunShop::request()->type;

        //手机号注册
        if ($type == 5) {
            $this->process();
        }
    }

    private function process()
    {
        $this->mobile   = \YunShop::request()->mobile;
        $this->password = \YunShop::request()->password;
        $this->uniacid  = \YunShop::app()->uniacid;

        if (SZ_YI_DEBUG) {
            $this->mobile   = '15046101656';
            $this->password = '123456';
        }

        //访问来自app分享
        $from = !empty(\YunShop::request()->from) ? \YunShop::request()->from : '';

        $yzShopSet = array('isreferral'=>0); //m('common')->getSysset('shop');

        $app = $this->getAppSet();

        if (!(\YunShop::app()->isajax) && !(\YunShop::app()->ispost) && $this->_validate()) {
            $member_info = MemberModel::getId($this->uniacid, $this->mobile);

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
                $referral = SubMemberModel::getInfo($this->uniacid, \YunShop::request()->referral);

                if (!$referral) {
                    return show_json(0, '推荐码无效！');
                } else {
                    $isreferraltrue = true;
                }
            }

            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $this->uniacid));

            $data = array(
                'uniacid' => $this->uniacid,
                'mobile' => $this->mobile,
                'groupid' => $default_groupid,
                'createtime' => TIMESTAMP,
                'nickname' => $this->mobile,
                'avatar' => "http://".$_SERVER ['HTTP_HOST']. '/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg',
                'gender' => 0,
                'nationality' => '',
                'resideprovince' => '',
                'residecity' => '',
            );
            $data['salt']  = Str::random(8);

            $data['password'] = md5($this->password. $data['salt'] . \YunShop::app()->config['setting']['authkey']);

            MemberModel::insertData($data);

            //使用推荐码 SH20160520172508468878
            $this->referral();

            $lifeTime = 24 * 3600 * 3;
            session_set_cookie_params($lifeTime);
            @session_start();
            $cookieid = "__cookie_sz_yi_userid_{$this->uniacid}";
            setcookie('member_mobile', $this->mobile);
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

    public function isLogged()
    {
        return !empty($_SESSION['member_id']);
    }

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

    /**
     * 使用推荐码
     */
    private function referral($isreferraltrue, $member_info, $referral)
    {
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

                SubMemberModel::updateDate($m_data, array("mobile" => $mobile, "uniacid" => $uniacid));
                $yzShopSet = m('common')->getSysset('shop');
                //todo   //m('member')->responseReferral($yzShopSet, $referral, $member);
            }
        }
    }
}