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
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\smsSendLimitModel;
use Illuminate\Support\Str;

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

        $this->process();
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

        if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost) && $this->_validate()) {
            $member_info = MemberModel::getId($this->uniacid, $this->mobile);

            if (!empty($member_info)) {
                return show_json(0, array('msg' => '该手机号已被注册'));
            }

            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $this->uniacid));

            $data = array(
                'uniacid' => $this->uniacid,
                'mobile' => $this->mobile,
                'groupid' => $default_groupid,
                'createtime' => TIMESTAMP,
                'nickname' => $this->mobile,
                'avatar' => SZ_YI_URL . 'template/mobile/default/static/images/photo-mr.jpg',
                'gender' => 0,
                'nationality' => '',
                'resideprovince' => '',
                'residecity' => '',
            );
            $data['salt']  = Str::random(8);

            $data['password'] = md5($this->password. $data['salt'] . \YunShop::app()->config['setting']['authkey']);

            $member_id = MemberModel::insertData($data);

            $cookieid = "__cookie_sz_yi_userid_{$this->uniacid}";
            Cookie::queue($cookieid, $member_id);
            session()->put('member_id', $member_id);

            return show_json(1, array('member_id', $member_id));
        } else {
            return show_json(0, array('msg' => '手机号或密码格式错误'));
        }
    }

    /**
     * 发送短信验证码
     *
     * @return array
     */
    public function sendCode()
    {
        $mobile = \YunShop::request()->mobile;
        if(empty($mobile)){
            return show_json(0, array('msg'=> '请填入手机号'));
        }

        if (!$this->smsSendLimit()) {
            return show_json(-1, array("msg" => "发送短信数量达到今日上限"));
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if(!empty($info))
        {
            return show_json(0, array('msg' => '该手机号已被注册！不能获取验证码'));
        }
        $code = rand(1000, 9999);

        session()->put('codetime', time());
        session()->put('code', $code);
        session()->put('code_mobile', $mobile);

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
                return show_json(0, array('msg' => $issendsms['SubmitResult']['msg']));
            }
        }
        else{
            if(isset($issendsms['result']['success'])){
                return show_json(1);
            }
            else{
                return show_json(0, array('msg' => $issendsms['msg']. '/' . $issendsms['sub_msg']));
            }
        }
    }

    /**
     * 检查验证码
     *
     * @return array
     */
    public function checkCode()
    {
        $code = \YunShop::request()->code;

        if((session('codetime')+60*5) < time()){
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

    /**
     * 验证手机号和密码
     *
     * @return bool
     */
    private function _validate()
    {
        if (!$this->smsSendLimit()) {
             return show_json(-1, array("msg" => "发送短信数量达到今日上限"));
        }

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
     * 短信发送限制
     *
     * 每天最多5条
     */
    private function smsSendLimit()
    {
        $curr_time = time();

        $uniacid = \YunShop::app()->uniacid;
        $mobile = \YunShop::request()->mobile;

        $mobile_info = smsSendLimitModel::getMobileInfo($uniacid, $mobile);

        if (!empty($mobile_info)) {
            $update_time = $mobile_info['created_at'];
            $total = $mobile_info['total'];

            if ($update_time <= $curr_time) {
                if (data('Ymd', $curr_time) == data('Ymd', $update_time)) {
                    if ($total <= 4) {
                        ++$total;
                        smsSendLimitModel::updateTotal(array($uniacid, $mobile), array($total));

                        return true;
                    }
                } else {
                    smsSendLimitModel::updateData(array($uniacid, $mobile), array(1, $curr_time));

                    return true;
                }
            }

        } else {
            smsSendLimitModel::insertData(array($uniacid, $mobile, 1, $curr_time));

            return true;
        }

        return false;
    }
}