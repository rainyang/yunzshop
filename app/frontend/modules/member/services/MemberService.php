<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/28
 * Time: 上午5:16
 */

namespace app\frontend\modules\member\services;

use app\common\models\Member;
use app\frontend\modules\member\models\smsSendLimitModel;
use Illuminate\Support\Facades\Cookie;

class MemberService
{
    private static $_current_member;
    public static function getCurrentMemberModel(){
        if(isset(self::$_current_member)){
            return self::$_current_member;
        }
        //todo 根据情况改写
        self::setCurrentMemberModel(9);
        return self::$_current_member;
    }

    public static function setCurrentMemberModel($member_id)
    {
        $member = Member::find($member_id);
        if(!isset($member)){
            return '用户id不存在';exit;
        }
        self::$_current_member = $member;
    }

    /**
     * 用户是否登录
     *
     * @return bool
     */
    public static function isLogged()
    {
        return !empty(session('member_id'));
    }

    /**
     * 验证手机号和密码
     *
     * @return bool
     */
    public static function validate($mobile, $password, $confirm_password = '')
    {
        if ($confirm_password == '') {
            $data = array(
                'mobile' => $mobile,
                'password' => $password
            );
            $check = array(
                'mobile' => array('required',
                    'digits:11',
                    'regex:/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1}))+\d{8})$/'
                ),
                'password' => 'required'
            );
        } else {
            $data = array(
                'mobile' => $mobile,
                'password' => $password,
                'confirm_password' => $confirm_password
            );
            $check = array(
                'mobile' => array('required',
                    'digits:11',
                    'regex:/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1}))+\d{8})$/'
                ),
                'password' => 'required',
                'confirm_password' => 'same:password'
            );
        }

        $validator = \Validator::make($data, $check);

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
    public static function smsSendLimit($uniacid, $mobile)
    {
        $curr_time = time();

        $mobile_info = smsSendLimitModel::getMobileInfo($uniacid, $mobile);

        if (!empty($mobile_info)) {
            $update_time = $mobile_info['created_at'];
            $total = $mobile_info['total'];

            if ((date('Ymd', $curr_time) == date('Ymd', $update_time))
                && $total < 5) {

                return true;
            }
        } else {
            $total = 0;
        }

        if ($total < 5) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 更新发送短信条数
     *
     * 每天最多5条
     */
    public static function udpateSmsSendTotal($uniacid, $mobile)
    {
        $curr_time = time();

        $mobile_info = smsSendLimitModel::getMobileInfo($uniacid, $mobile);

        if (!empty($mobile_info)) {
            $update_time = $mobile_info['created_at'];
            $total = $mobile_info['total'];

            if ($update_time <= $curr_time) {
                if (date('Ymd', $curr_time) == date('Ymd', $update_time)) {
                    if ($total <= 4) {
                        ++$total;

                        smsSendLimitModel::updateData(array(
                            'uniacid' => $uniacid,
                            'mobile' => $mobile), array(
                            'total' => $total,
                            'created_at' => $curr_time));
                    }
                } else {
                    smsSendLimitModel::updateData(array(
                        'uniacid' => $uniacid,
                        'mobile' => $mobile), array(
                        'total' => 1,
                        'created_at' => $curr_time));
                }
            }
        } else {
            smsSendLimitModel::insertData(array(
                    'uniacid' => $uniacid,
                    'mobile' => $mobile,
                    'total' => 1,
                    'created_at' => $curr_time)
            );
        }
    }

    /**
     * 阿里大鱼
     *
     * @param $sms
     * @param $templateType
     * @return array
     */
    public static function send_sms_alidayu($sms, $templateType)
    {
        switch ($templateType) {
            case 'reg':
                $templateCode = $sms['templateCode'];
                $params = @explode("\n", $sms['product']);
                break;
            case 'forget':
                $templateCode = $sms['templateCodeForget'];
                $params = @explode("\n", $sms['forget']);
                break;
            default:
                $params = array();
                $templateCode = $sms['templateCode'];
                break;
        }
        return array('templateCode' => $templateCode, 'params' => $params);
    }

    /**
     * 互亿无线
     *
     * @param $account
     * @param $pwd
     * @param $mobile
     * @param $code
     * @param string $type
     * @param $name
     * @param $title
     * @param $total
     * @param $tel
     * @return mixed
     */
    public static function send_sms($account, $pwd, $mobile, $code, $type = 'check', $name, $title, $total, $tel)
    {
        if ($type == 'check') {
            $content = "您的验证码是：" . $code . "。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        } elseif ($type == 'verify') {
            $verify_set = $sms = Setting::get('shop.sms');
            $allset = iunserializer($verify_set['plugins']);
            if (is_array($allset) && !empty($allset['verify']['code_template'])) {
                $content = sprintf($allset['verify']['code_template'], $code, $title, $total, $name, $mobile, $tel);
            } else {
                $content = "提醒您，您的核销码为：" . $code . "，订购的票型是：" . $title . "，数量：" . $total . "张，购票人：" . $name . "，电话：" . $mobile . "，门店电话：" . $tel . "。请妥善保管，验票使用！";

            }

        }

        $smsrs = file_get_contents('http://106.ihuyi.cn/webservice/sms.php?method=Submit&account=' . $account . '&password=' . $pwd . '&mobile=' . $mobile . '&content=' . urldecode($content));
        return xml_to_array($smsrs);
    }

    function xml_to_array($xml)
    {
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if (preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $subxml = $matches[2][$i];
                $key = $matches[1][$i];
                if (preg_match($reg, $subxml)) {
                    $arr[$key] = xml_to_array($subxml);
                } else {
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }

    protected function save($member_info, $uniacid)
    {
        //$member_info = $member_info->toArray();
        $cookieid = "__cookie_sz_yi_userid_{$uniacid}";

        if (is_app()) {
            Cookie::queue($cookieid, $member_info['uid'], time()+3600*24*7);
        } else {
            Cookie::queue($cookieid, $member_info['uid']);
        }

        Cookie::queue('member_id', $member_info['uid']);

        if(!isMobile()){
            $member_name = !empty($member_info['realname']) ? $member_info['realname'] : $member_info['nickname'];
            $member_name = !empty($member_name) ? $member_name : "未知";
            session()->put('member_id',$member_info['uid']);
            session()->put('member_name',$member_name);
        }

        return show_json(1, array(
            'member_id' => $member_info['uid'],
        ));
    }
}