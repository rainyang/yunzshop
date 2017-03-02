<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\models\MemberModel;
use app\backend\modules\system\modules\SyssetModel;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Support\Str;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class RegisterController extends BaseController
{
    private $mobile;
    private $password;
    private $confirm_password;
    private $uniacid;

    public function index()
    {
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> session('member_id')));
        }

        $type = \YunShop::request()->type;

        //手机号注册
        if ($type == 5) {
            $this->process();
        } else {
            return show_json(0, 'api请求错误');
        }
    }

    private function process()
    {
        $this->mobile   = \YunShop::request()->mobile;
        $this->password = \YunShop::request()->password;
        $this->confirm_password = \YunShop::request()->confirm_password;
        $this->uniacid  = \YunShop::app()->uniacid;

        if (SZ_YI_DEBUG) {
            $this->mobile   = '15046101651';
            $this->password = '123456';
            $this->confirm_password = '123456';
        }

        //访问来自app分享
        $from = !empty(\YunShop::request()->from) ? \YunShop::request()->from : '';

        $yzShopSet = array('isreferral'=>0); //m('common')->getSysset('shop');

        $app = $this->getAppSet();

        if (!(\YunShop::app()->isajax) && !(\YunShop::app()->ispost) && $this->_validate()) {
            $member_info = MemberModel::getId($this->uniacid, $this->mobile);

            if (!empty($member_info)) {
               // $cookieJar = new CookieJar();
               // $cookieJar->queue(cookie('user_id', '00001'));
             //   Cookie::queue('test', null , -1); // 销毁
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
            $this->referral($isreferraltrue, $member_info, $referral);

            $lifeTime = 24 * 3600 * 3;
            $cookieid = "__cookie_sz_yi_userid_{$this->uniacid}";
            $response = new Response();
            $response->withCookie(Cookie::make($cookieid, $member_info['uid'], $lifeTime));

            if(empty($preUrl))
            {
                $preUrl =Url::app('shop.index');
            }

            if ($from == 'app') {
                $preUrl = Url::app('shop.download');
            }

            return show_json(1, $preUrl);
        } else {
            return show_json(0, '手机号或密码格式错误！');
        }
    }

    /**
     * 发送短信验证码
     *
     * @return array
     */
    public function sendcode()
    {
        $mobile = \YunShop::request()->mobile;
        if(empty($mobile)){
            return show_json(0, '请填入手机号');
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if(!empty($info))
        {
            return show_json(0, '该手机号已被注册！不能获取验证码。');
        }
        $code = rand(1000, 9999);

        session(['codetime'=>time()]);
        session(['code'=>$code]);
        session(['code_mobile'=>$mobile]);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";
        $issendsms = $this->sendSms($mobile, $code);
        //print_r($issendsms);

        $set = m('common')->getSysset();
        //互亿无线
        if($set['sms']['type'] == 1){
            if($issendsms['SubmitResult']['code'] == 2){
                return show_json(1);
            }
            else{
                return show_json(0, $issendsms['SubmitResult']['msg']);
            }
        }
        else{
            if(isset($issendsms['result']['success'])){
                return show_json(1);
            }
            else{
                return show_json(0, $issendsms['msg']. '/' . $issendsms['sub_msg']);
            }
        }
    }

    /**
     * 检查验证码
     *
     * @return array
     */
    public function checkcode()
    {
        $code = \YunShop::request()->code;

        if((session(codetime)+60*5) < time()){
            return show_json(0, '验证码已过期,请重新获取');
        }
        if(session('code') != $code){
            return show_json(0, '验证码错误,请重新获取');
        }
        return show_json(1);
    }

    /**
     * 用户是否登录
     *
     * @return bool
     */
    public function isLogged()
    {
        return !empty(session('member_id'));
    }

    private function _validate()
    {
        $data = array(
            'mobile' => $this->mobile,
            'password' => $this->password,
            'confirm_password' => $this->confirm_password
        );
        $validator = \Validator::make($data, array(
            'mobile' => array('required',
                              'digits:11',
                               'regex:/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1}))+\d{8})$/'
            ),
            'password' => 'required',
            'confirm_password' => 'same:password'
        ));

        if ($validator->fails()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取app设置信息
     *
     * @return mixed
     */
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
     *
     * @param $isreferraltrue
     * @param $member_info
     * @param $referral
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

                SubMemberModel::updateDate($m_data, array("mobile" => $this->mobile, "uniacid" => $this->uniacid));
                $yzShopSet = m('common')->getSysset('shop');
                //todo   //m('member')->responseReferral($yzShopSet, $referral, $member);
            }
        }
    }
}