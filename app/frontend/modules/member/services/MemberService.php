<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/28
 * Time: 上午5:16
 */

namespace app\frontend\modules\member\services;

use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\models\Member;
use app\common\models\MemberGroup;
use app\common\models\MemberShopInfo;
use app\common\services\Session;
use app\frontend\models\McGroupsModel;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\smsSendLimitModel;
use app\frontend\modules\member\models\SubMemberModel;
use Illuminate\Support\Facades\Cookie;

class MemberService
{

    private static $_current_member;

    public static function getCurrentMemberModel(){
        if(isset(self::$_current_member)){
            return self::$_current_member;
        }
        $uid = \YunShop::app()->getMemberId();
        if(!isset($uid)){
            throw new AppException('uid不存在');
        }
        self::setCurrentMemberModel($uid);
        return self::$_current_member;
    }

    public static function setCurrentMemberModel($member_id)
    {
        $member = \app\frontend\models\Member::find($member_id);
        if(!isset($member)){
            throw new AppException('(ID:'.$member_id.')用户不存在');
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
        return \YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0;
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
            $rules = array(
                'mobile' => 'regex:/^1[34578]\d{9}$/',
                'password' => 'required|min:6|regex:/^[A-Za-z0-9@!#\$%\^&\*]+$/'
            );
            $message = array(
                'regex'    => ':attribute 格式错误',
                'required' => ':attribute 不能为空',
                'min' => ':attribute 最少6位'
            );
            $attributes = array(
                "mobile" => '手机号',
                'password' => '密码',
            );
        } else {
            $data = array(
                'mobile' => $mobile,
                'password' => $password,
                'confirm_password' => $confirm_password
            );
            $rules = array(
                'mobile' => 'regex:/^1[34578]\d{9}$/',
                'password' => 'required|min:6|regex:/^[A-Za-z0-9@!#\$%\^&\*]+$/',
                'confirm_password' => 'same:password'
            );
            $message = array(
                'regex'    => ':attribute 格式错误',
                'required' => ':attribute 不能为空',
                'min' => ':attribute 最少6位',
                'same' => ':attribute 不匹配'
            );
            $attributes = array(
                "mobile" => '手机号',
                'password' => '密码',
                'confirm_password' => '密码',
            );
        }

        $validate = \Validator::make($data,$rules,$message,$attributes);

        if ($validate->fails()) {
            $warnings = $validate->messages();
            $show_warning = $warnings->first();

            return show_json('0', $show_warning);
        } else {
            return show_json('1');
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

            if ((date('Ymd', $curr_time) != date('Ymd', $update_time))) {

                $total = 0;
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
                    if ($total <= 5) {
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

    /**
     * pc端注册 保存信息
     *
     * @param $member_info
     * @param $uniacid
     */
    protected function save($member_info, $uniacid)
    {
        Session::set('member_id', $member_info['uid']);

        $cookieid = "__cookie_yun_shop_userid_{$uniacid}";

        setcookie('member_id', $member_info['uid'],'3600 * 24' + time(),'/');
        Cookie::queue($cookieid, $member_info['uid']);
        Cookie::queue('member_id', $member_info['uid']);
    }

    /**
     * 检查验证码
     *
     * @return array
     */
    public static function checkCode()
    {
        $code = \YunShop::request()->code;

        if ((Session::get('codetime') + 60 * 5) < time()) {
            return show_json('0', '验证码已过期,请重新获取');
        }
        if (Session::get('code') != $code) {
            return show_json('0', '验证码错误,请重新获取');
        }
        return show_json('1');
    }

    /**
     * 公众号开放平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function unionidLogin($uniacid, $userinfo, $upperMemberId = NULL, $loginType)
    {
        $member_id = 0;
        $userinfo['nickname'] = $this->filteNickname($userinfo);

        $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid'])->first();
\Log::debug('----------UnionidInfo-------', $UnionidInfo);
        if (!is_null($UnionidInfo)) {
            $member_id = $UnionidInfo->member_id;
        }

        //$mc_mapping_fans_model = McMappingFansModel::getUId($userinfo['openid']);
        $mc_mapping_fans_model = $this->getFansModel($userinfo['openid']);
        $member_model = Member::getMemberById($member_id);
        $member_shop_info_model = MemberShopInfo::getMemberShopInfo($member_id);

        if (!empty($UnionidInfo->unionid) && !empty($member_model)
            && !empty($mc_mapping_fans_model) && !empty($member_shop_info_model)) {
            \Log::debug('微信登陆更新');

            $types = explode('|', $UnionidInfo->type);
            $member_id = $UnionidInfo->member_id;
\Log::debug('explode type', $types);
            if (!in_array($loginType, $types)) {
                \Log::debug(sprintf('保存type-%s', $UnionidInfo->type . '|' . $loginType));
                //更新ims_yz_member_unique表
                MemberUniqueModel::updateData(array(
                    'unique_id' => $UnionidInfo->unique_id,
                    'type' => $UnionidInfo->type . '|' . $loginType
                ));
            }

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if (empty($member_model) && empty($mc_mapping_fans_model)) {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }
            } elseif ($mc_mapping_fans_model->uid) {
                $member_id = $mc_mapping_fans_model->uid;

                $this->updateMemberInfo($member_id, $userinfo);
            } else {
                $this->addFansMember($member_id, $uniacid, $userinfo);
            }

            if (empty($member_shop_info_model)) {
                $this->addSubMemberInfo($uniacid, $member_id);
            }

            if (empty($UnionidInfo->unionid)) {
                //添加ims_yz_member_unique表
                $this->addMemberUnionid($uniacid, $member_id, $userinfo['unionid']);
            }

            //生成分销关系链
            if ($upperMemberId) {
                Member::createRealtion($member_id, $upperMemberId);
            } else {
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

    /**
     * 公众号平台授权登陆
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function openidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = 0;
        $userinfo['nickname'] = $this->filteNickname($userinfo);
        //$fans_mode = McMappingFansModel::getUId($userinfo['openid']);
        $fans_mode = $this->getFansModel($userinfo['openid']);

        if ($fans_mode) {
            $member_model = Member::getMemberById($fans_mode->uid);
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($fans_mode->uid);

            $member_id = $fans_mode->uid;
        }

        if ((!empty($member_model)) && (!empty($fans_mode) && !empty($member_shop_info_model))) {
            \Log::debug('微信登陆更新');

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if (empty($member_model) && empty($fans_mode)) {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }
            } elseif ($fans_mode->uid) {
                $member_id = $fans_mode->uid;

                $this->updateMemberInfo($member_id, $userinfo);
            }

            if (empty($member_shop_info_model)) {
                $this->addSubMemberInfo($uniacid, $member_id);
            }

            //生成分销关系链
            if ($upperMemberId) {
                \Log::debug('分销关系链-海报');
                Member::createRealtion($member_id, $upperMemberId);
            } else {
                \Log::debug('分销关系链-链接');
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

    /**
     * 过滤微信用户名特殊符号
     *
     * @param $userinfo
     * @return mixed
     */
    public function filteNickname($userinfo)
    {
        if (Client::getOS() == 'OS_WIN') {
            $s_format = 'UCS-2';
        } else {
            $s_format = 'UCS-2BE';
        }

        $patten = "/(\\\u[ed][0-9a-f]{3})/ie";

        $nickname = json_encode($userinfo['nickname']);
        \Log::debug('nickname', [$nickname]);
        $nickname = preg_replace($patten, "", $nickname);
        \Log::debug('pre', [$nickname]);
        $nickname = preg_replace("#\\\u([0-9a-f]+)#ie","iconv('{$s_format}','UTF-8', pack('H4', '\\1'))",$nickname);
        \Log::debug('post', [$nickname]);
        \Log::debug('json', [json_decode($this->cutNickname($nickname))]);
        return json_decode($this->cutNickname($nickname));
    }

    /**
     * 截取字符串长度
     *
     * @param $nickname
     * @return string
     */
    public function cutNickname($nickname)
    {
        if (mb_strlen($nickname) > 18) {
            return mb_substr($nickname, 0, 18);
        }

        return $nickname;
    }

    /**
     * 会员基础表操作
     *
     * @param $uniacid
     * @param $userinfo
     * @return mixed
     */
    public function addMemberInfo($uniacid, $userinfo)
    {
        //添加mc_members表
        $default_group = McGroupsModel::getDefaultGroupId();
        $uid = MemberModel::insertData($userinfo, array(
            'uniacid' => $uniacid,
            'groupid' => $default_group->groupid
        ));
        \Log::debug('add mapping fans');
        //添加mapping_fans表
        /*McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));*/

        return $uid;
    }

    /**
     * 会员辅助表操作
     *
     * @param $uniacid
     * @param $member_id
     */
    public function addSubMemberInfo($uniacid, $member_id)
    {
        //添加yz_member表
        $default_sub_group_id = MemberGroup::getDefaultGroupId()->first();

        if (!empty($default_sub_group_id)) {
            $default_subgroup_id = $default_sub_group_id->id;
        } else {
            $default_subgroup_id = 0;
        }

        SubMemberModel::insertData(array(
            'member_id' => $member_id,
            'uniacid' => $uniacid,
            'group_id' => $default_subgroup_id,
            'level_id' => 0,
        ));
    }

    /**
     * 会员关联表操作
     *
     * @param $uniacid
     * @param $member_id
     * @param $unionid
     */
    public function addMemberUnionid($uniacid, $member_id, $unionid)
    {
        MemberUniqueModel::insertData(array(
            'uniacid' => $uniacid,
            'unionid' => $unionid,
            'member_id' => $member_id,
            'type' => self::LOGIN_TYPE
        ));
    }

    /**
     * 更新微信用户信息
     *
     * @param $member_id
     * @param $userinfo
     */
    public function updateMemberInfo($member_id, $userinfo)
    {
        //更新mc_members
        $mc_data = array(
            'nickname' => stripslashes($userinfo['nickname']),
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
            'nationality' => $userinfo['country'],
            'resideprovince' => $userinfo['province'] . '省',
            'residecity' => $userinfo['city'] . '市'
        );

        MemberModel::updataData($member_id, $mc_data);

        //更新mapping_fans
        /*$record = array(
            'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => isset($userinfo['subscribe'])?:0,
            'tag' => base64_encode(serialize($userinfo))
        );

        McMappingFansModel::updateData($member_id, $record);*/
    }

    /**
     * 登陆处理
     *
     * @param $userinfo
     *
     * @return integer
     */
    public function memberLogin($userinfo, $upperMemberId = NULL)
    {
        if (is_array($userinfo) && !empty($userinfo['unionid'])) {
            \Log::debug('---开放平台入口----');
            $member_id = $this->unionidLogin(\YunShop::app()->uniacid, $userinfo, $upperMemberId);
        } elseif (is_array($userinfo) && !empty($userinfo['openid'])) {
            \Log::debug('---公众号入口----');
            $member_id = $this->openidLogin(\YunShop::app()->uniacid, $userinfo, $upperMemberId);
        }

        return $member_id;
    }
}