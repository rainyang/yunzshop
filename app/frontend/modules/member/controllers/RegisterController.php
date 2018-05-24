<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\helpers\Url;
use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\services\aliyun\AliyunSMS;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\services\MemberService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use iscms\Alisms\SendsmsPusher as Sms;
use app\common\exceptions\AppException;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Mews\Captcha\Captcha;


class RegisterController extends ApiController
{
    protected $publicController = ['Register'];
    protected $publicAction = ['index', 'sendCode', 'sendCodeV2', 'checkCode', 'sendSms', 'changePassword'];
    protected $ignoreAction = ['index', 'sendCode', 'sendCodeV2', 'checkCode', 'sendSms', 'changePassword'];

    public function index()
    {
        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->confirm_password;
        $uniacid = \YunShop::app()->uniacid;
        $captcha = \YunShop::request()->captcha;

        if ((\Request::getMethod() == 'POST')) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            //验证码
            $rules = [
                'captcha' => 'required|captcha'
            ];

            $messages = [
                'captcha.required' => '请输入验证码',
                'captcha.captcha' => '验证码错误，请重试'
            ];

            $validator = \Validator::make($captcha, $rules, $messages);
            if ($validator->fails()) {
                return $this->errorJson('验证码错误', $validator);
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            $member_info = MemberModel::getId($uniacid, $mobile);

            if (!empty($member_info)) {
                return $this->errorJson('该手机号已被注册');
            }

            //添加mc_members表
            $default_groupid = MemberGroup::getDefaultGroupId($uniacid)->first();

            $member_set = \Setting::get('shop.member');

            if (isset($member_set) && $member_set['headimg']) {
                $avatar = replace_yunshop(tomedia($member_set['headimg']));
            } else {
                $avatar = Url::shopUrl('static/images/photo-mr.jpg');
            }

            $data = array(
                'uniacid' => $uniacid,
                'mobile' => $mobile,
                'groupid' => $default_groupid->id ? $default_groupid->id : 0,
                'createtime' => time(),
                'nickname' => $mobile,
                'avatar' => $avatar,
                'gender' => 0,
                'residecity' => '',
            );
            $data['salt'] = Str::random(8);

            $data['password'] = md5($password . $data['salt']);

            $memberModel = MemberModel::create($data);
            $member_id = $memberModel->uid;

            //添加yz_member表
            $default_sub_group_id = MemberGroup::getDefaultGroupId()->first();

            if (!empty($default_sub_group_id)) {
                $default_subgroup_id = $default_sub_group_id->id;
            } else {
                $default_subgroup_id = 0;
            }

            $sub_data = array(
                'member_id' => $member_id,
                'uniacid' => $uniacid,
                'group_id' => $default_subgroup_id,
                'level_id' => 0,
            );
            SubMemberModel::insertData($sub_data);

            $cookieid = "__cookie_yun_shop_userid_{$uniacid}";
            Cookie::queue($cookieid, $member_id);
            Session::set('member_id', $member_id);

            $password = $data['password'];
            $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();
            $yz_member = MemberShopInfo::getMemberShopInfo($member_id)->toArray();

            $data = MemberModel::userData($member_info, $yz_member);
            //app注册添加member_wechat表中数据
            $type = \YunShop::request()->type;
            if ($type == 7) {
                $uuid = \YunShop::request()->uuid;
                MemberWechatModel::insertData(array(
                    'uniacid' => $uniacid,
                    'member_id' => $member_info['uid'],
                    'openid' => $member_info['mobile'],
                    'nickname' => $member_info['nickname'],
                    'gender' => $member_info['gender'],
                    'avatar' => $member_info['avatar'],
                    'province' => $member_info['resideprovince'],
                    'city' => $member_info['residecity'],
                    'country' => $member_info['nationality'],
                    'uuid' => $uuid
                ));
            }
            return $this->successJson('', $data);
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }

    /**
     * 发送短信验证码
     *
     *
     */
    public function sendCode()
    {
        $mobile = \YunShop::request()->mobile;

        $reset_pwd = \YunShop::request()->reset;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if (!empty($info) && empty($reset_pwd)) {
            return $this->errorJson('该手机号已被注册！不能获取验证码');
        }
        $code = rand(1000, 9999);

        Session::set(codetime, time());
        Session::set(code, $code);
        Session::set(code_mobile, $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            $this->sendSms($mobile, $code);
        }
    }

    public function sendCodeV2()
    {
        $mobile = \YunShop::request()->mobile;

        $reset_pwd = \YunShop::request()->reset;

        $state = \YunShop::request()->state ?: '86';

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $info = MemberModel::getId(\YunShop::app()->uniacid, $mobile);

        if (!empty($info) && empty($reset_pwd)) {
            return $this->errorJson('该手机号已被注册！不能获取验证码');
        }
        $code = rand(1000, 9999);

        Session::set(codetime, time());
        Session::set(code, $code);
        Session::set(code_mobile, $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            $this->sendSmsV2($mobile, $code, $state);
        }
    }

    public function sendWithdrawCode()
    {
        $mobile = \YunShop::request()->mobile;
        $reset_pwd = \YunShop::request()->reset;

        if (empty($mobile)) {
            return $this->errorJson('请填入手机号');
        }

        $info = MemberShopInfo::getUserInfo($mobile);

        if (!empty($info)) {
            return $this->errorJson('该手机号已被注册！不能获取验证码');
        }
        $code = rand(1000, 9999);

        Session::set(codetime, time());
        Session::set(code, $code);
        Session::set(code_mobile, $mobile);

        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        if (!MemberService::smsSendLimit(\YunShop::app()->uniacid, $mobile)) {
            return $this->errorJson('发送短信数量达到今日上限');
        } else {
            $this->sendSms($mobile, $code);
        }
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
            $issendsms = MemberService::send_sms(trim($sms['account']), trim($sms['password']), $mobile, $code);

            if ($issendsms['SubmitResult']['code'] == 2) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson('短信设置'.$issendsms['SubmitResult']['msg'].','.'请前往设置');
            }
        } elseif ($sms['type'] == 2) {
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

            $top_client = new \iscms\AlismsSdk\TopClient(trim($sms['appkey']), trim($sms['secret']));
            $name = trim($sms['signname']);
            $templateCode = trim($sms['templateCode']);

            config([
                'alisms.KEY' => trim($sms['appkey']),
                'alisms.SECRETKEY' => trim($sms['secret'])
            ]);

            $sms = new Sms($top_client);
            $issendsms = $sms->send($mobile, $name, $content, $templateCode);

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                //return $this->errorJson($issendsms->msg . '/' . $issendsms->sub_msg);
            }
        } elseif ($sms['type'] == 3) {
            $aly_sms = new AliyunSMS(trim($sms['aly_appkey']), trim($sms['aly_secret']));

            $response = $aly_sms->sendSms(
                $sms['aly_signname'], // 短信签名
                $sms['aly_templateCode'], // 短信模板编号
                $mobile, // 短信接收者
                Array(  // 短信模板中字段的值
                    "number" => $code
                )
            );

            if ($response->Code == 'OK' && $response->Message == 'OK') {
                return $this->successJson();
            } else {
                return $this->errorJson($response->Message);
            }

        } else {
            return $this->errorJson('未设置短信功能');
        }
    }

    public function sendSmsV2($mobile, $code, $state, $templateType = 'reg')
    {
        $sms = \Setting::get('shop.sms');

        //互亿无线
        if ($sms['type'] == 1) {
            if ($state != '86') {
                $account = trim($sms['account2']);
                $password = trim($sms['password2']);
            } else {
                $account = trim($sms['account']);
                $password = trim($sms['password']);
            }
            $issendsms = MemberService::send_smsV2($account, $password, $mobile, $code, $state);

            if ($issendsms['SubmitResult']['code'] == 2) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                return $this->errorJson('短信设置'.$issendsms['SubmitResult']['msg'].','.'请前往设置');
            }
        } elseif ($sms['type'] == 2) {
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

            $top_client = new \iscms\AlismsSdk\TopClient(trim($sms['appkey']), trim($sms['secret']));
            $name = trim($sms['signname']);
            $templateCode = trim($sms['templateCode']);

            config([
                'alisms.KEY' => trim($sms['appkey']),
                'alisms.SECRETKEY' => trim($sms['secret'])
            ]);

            $sms = new Sms($top_client);
            $issendsms = $sms->send($mobile, $name, $content, $templateCode);

            if (isset($issendsms->result->success)) {
                MemberService::udpateSmsSendTotal(\YunShop::app()->uniacid, $mobile);
                return $this->successJson();
            } else {
                //return $this->errorJson($issendsms->msg . '/' . $issendsms->sub_msg);
            }
        } elseif ($sms['type'] == 3) {
            $aly_sms = new AliyunSMS(trim($sms['aly_appkey']), trim($sms['aly_secret']));

            $response = $aly_sms->sendSms(
                $sms['aly_signname'], // 短信签名
                $sms['aly_templateCode'], // 短信模板编号
                $mobile, // 短信接收者
                Array(  // 短信模板中字段的值
                    "number" => $code
                )
            );

            if ($response->Code == 'OK' && $response->Message == 'OK') {
                return $this->successJson();
            } else {
                return $this->errorJson($response->Message);
            }

        } else {
            return $this->errorJson('未设置短信功能');
        }
    }

    /**
     * 短信验证
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCode()
    {
        $mobile = \YunShop::request()->mobile;
        $uniacid = \YunShop::app()->uniacid;

        $check_code = MemberService::checkCode();
        $member_info = MemberModel::getId($uniacid, $mobile);

        if (empty($member_info)) {
            return $this->errorJson('手机号不存在');
        }

        if ($check_code['status'] != 1) {
            return $this->errorJson($check_code['json']);
        }

        return $this->successJson('ok');
    }

    /**
     * 修改密码
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword()
    {
        $mobile = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        $confirm_password = \YunShop::request()->confirm_password;
        $uniacid = \YunShop::app()->uniacid;

        if ((\Request::getMethod() == 'POST')) {
            $check_code = MemberService::checkCode();

            if ($check_code['status'] != 1) {
                return $this->errorJson($check_code['json']);
            }

            $msg = MemberService::validate($mobile, $password, $confirm_password);

            if ($msg['status'] != 1) {
                return $this->errorJson($msg['json']);
            }

            $member_info = MemberModel::getId($uniacid, $mobile);

            if (empty($member_info)) {
                return $this->errorJson('该手机号不存在');
            }

            //更新密码
            $data['salt'] = Str::random(8);
            $data['password'] = md5($password . $data['salt']);

            MemberModel::updataData($member_info->uid, $data);
            $member_id = $member_info->uid;

            $password = $data['password'];
            $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();
            $yz_member = MemberShopInfo::getMemberShopInfo($member_id)->toArray();

            $data = MemberModel::userData($member_info, $yz_member);

            return $this->successJson('', $data);
        } else {
            return $this->errorJson('手机号或密码格式错误');
        }
    }
}