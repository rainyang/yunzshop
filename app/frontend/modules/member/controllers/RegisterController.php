<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\helpers\Url;
use Illuminate\Support\Facades\Cookie;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\services\MemberService;
use app\common\models\MemberGroup;
use Illuminate\Support\Str;
use iscms\Alisms\SendsmsPusher as Sms;
use Setting;

class RegisterController extends BaseController
{
    public function index()
    {
        if (MemberService::isLogged()) {
            return $this->errorJson('会员已登录');
        }

        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->confirm_password;
        $uniacid = \YunShop::app()->uniacid;


        if (($_SERVER['REQUEST_METHOD'] == 'POST')
            && MemberService::validate($mobile, $password, $confirm_password)
        ) {
            $member_info = MemberModel::getId($uniacid, $mobile);

            if (!empty($member_info)) {
                return $this->errorJson('该手机号已被注册');
            }

            $default_groupid = MemberGroup::getDefaultGroupId($uniacid)->first();

            $data = array(
                'uniacid' => $uniacid,
                'mobile' => $mobile,
                'groupid' => $default_groupid->id ? $default_groupid->id : 0,
                'createtime' => time(),
                'nickname' => $mobile,
                'avatar' => Url::shopUrl('static/images/photo-mr.jpg'),
                'gender' => 0,
                'residecity' => '',
            );
            $data['salt'] = Str::random(8);

            $data['password'] = md5($password . $data['salt']);

            $memberModel = MemberModel::create($data);
            $member_id = $memberModel->uid;

            $cookieid = "__cookie_yun_shop_userid_{$uniacid}";
            Cookie::queue($cookieid, $member_id);
            session()->put('member_id', $member_id);

            return $this->successJson(['member_id' => $member_id]);
        } else {
            return $this->errorJson('手机号或密码格式错误');
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
        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if (!empty($info)) {
            return $this->errorJson('该手机号已被注册！不能获取验证码');
        }
        $code = rand(1000, 9999);

        session()->put('codetime', time());
        session()->put('code', $code);
        session()->put('code_mobile', $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            $this->sendSms($mobile, $code);
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

        if ((session('codetime') + 60 * 5) < time()) {
            return $this->errorJson('验证码已过期,请重新获取');
        }
        if (session('code') != $code) {
            return $this->errorJson('验证码错误,请重新获取');
        }
        return $this->successJson();
    }

    /**
     * 发送短信
     *
     * @param $mobile
     * @param $code
     * @param string $templateType
     * @return array|mixed
     */
    public function sendSms($mobile, $code, $templateType = 'reg')
    {
        $sms = \Setting::get('shop.sms');

        //互亿无线
        if ($sms['type'] == 1) {
            $issendsms = MemberService::send_sms($sms['account'], $sms['password'], $mobile, $code);

            if ($issendsms['SubmitResult']['code'] == 2) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson($issendsms['SubmitResult']['msg']);
            }
        } else {
            $result = MemberService::send_sms_alidayu($sms, $templateType);

            if (count($result['params']) > 1) {
                $nparam['code'] = "{$code}";
                foreach ($result['params'] as $param) {
                    $param = trim($param);
                    $explode_param = explode("=", $param);
                    if (!empty($explode_param[0])) {
                        $nparam[$explode_param[0]] = "{$explode_param[1]}";
                    }
                }

                $content = json_encode($nparam);
            } else {
                $explode_param = explode("=", $result['params'][0]);
                $content = json_encode(array('code' => (string)$code, 'product' => $explode_param[1]));
            }

            $top_client = new \iscms\AlismsSdk\TopClient($sms['appkey'], $sms['secret']);
            $name = $sms['signname'];
            $templateCode = $sms['templateCode'];

            config([
                'alisms.KEY' => $sms['appkey'],
                'alisms.SECRETKEY' => $sms['secret']
            ]);
            $sms = new Sms($top_client);
            $issendsms = $sms->send($mobile, $name, $content, $templateCode);

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson($issendsms->msg . '/' . $issendsms->sub_msg);
            }
        }
    }
}