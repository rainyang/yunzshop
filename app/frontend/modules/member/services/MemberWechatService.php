<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午11:21
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberWechatModel;
use Illuminate\Session\Store;

class MemberWechatService extends MemberService
{
    private $_app_id;
    private $_appSecret;
    private $_login_type    = 4;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_app_id = '';
        $this->_appSecret = '';
    }

    public function login()
    {
        $uniacid = \YunApp::app()->uniacid;

        $callback  =  \YunShop::app()->siteroot . 'app/index.php?' . $_SERVER['QUERY_STRING'];

        //微信登录
        //-------生成唯一随机串防CSRF攻击
        $state  = md5(uniqid(rand(), TRUE));
        session()->put("wx_state", $state);

        $callback = urlencode($callback);

        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->_app_id."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";

        if (!empty(\YunShop::request()->code)) {
            $user_info = $this->getUserInfo(\YunShop::request()->code);

            if (is_array($user_info) && !empty($user_info['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $user_info['unionid']);

                if (!empty($UnionidInfo['unionid'])) {
                    $types = explode('|',$UnionidInfo['type']);
                    $member_id = $UnionidInfo['member_id'];

                    if (!in_array($this->_login_type, $types)) {
                        //更新ims_yz_member_unique表
                        MemberUniqueModel::updateData(array(
                            'unique_id'=>$UnionidInfo['unique_id'],
                            'type' => $UnionidInfo['type'] . '|' . $this->_login_type
                        ));

                        //添加yz_member_wechat表
                        MemberWechatModel::insertData(array(
                            'uniacid' => $uniacid,
                            'member_id' => $UnionidInfo['member_id'],
                            'openid' => $user_info['openid'],
                            'nickname' => $user_info['nickname'],
                            'avatar' => $user_info['headimgurl'],
                            'gender' => $user_info['sex'],
                            'nationality' => $user_info['country'],
                            'resideprovince' => $user_info['province'] . '省',
                            'residecity' => $user_info['city'] . '市',
                            'created_at' => time()
                        ));
                    }
                } else {
                    //添加ims_mc_member表
                    $member_id = MemberModel::insertData(array(
                        'uniacid' => $uniacid,
                        'groupid' => $user_info['unionid'],
                        'createtime' => TIMESTAMP,
                        'nickname' => $user_info['nickname'],
                        'avatar' => $user_info['headimgurl'],
                        'gender' => $user_info['sex'],
                        'nationality' => $user_info['country'],
                        'resideprovince' => $user_info['province'] . '省',
                        'residecity' => $user_info['city'] . '市'
                    ));


                    //添加ims_yz_member_unique表
                    MemberUniqueModel::insertData(array(
                        'uniacid' => $uniacid,
                        'unionid' => $user_info['unionid'],
                        'member_id' => $member_id,
                        'type' => $this->_login_type
                    ));

                    //添加yz_member_wechat表
                    MemberWechatModel::insertData(array(
                        'uniacid' => $uniacid,
                        'member_id' => $member_id,
                        'openid' => $user_info['openid'],
                        'nickname' => $user_info['nickname'],
                        'avatar' => $user_info['headimgurl'],
                        'gender' => $user_info['sex'],
                        'nationality' => $user_info['country'],
                        'resideprovince' => $user_info['province'] . '省',
                        'residecity' => $user_info['city'] . '市',
                        'created_at' => time()
                    ));
                }

                session()->put('member_id',$member_id);
            } else {
                show_json(0, array('url'=> $wxurl));
            }
        } else {
            show_json(0, array('url'=> $wxurl));
        }
    }

    /**
     * pc端微信登录获取用户信息
     *
     * @return array|mixed|stdClass
     */
    public function getUserInfo($code)
    {
        if (\YunShop::request()->state != session("wx_state")) {
            exit("5001");
        }

        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->_app_id . '&secret=' . $this->_appSecret . '&code=' . $code . '&grant_type=authorization_code';
        $resp     = @ihttp_get($token_url);
        $token      = @json_decode($resp['content'], true);

        $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token['access_token'] . '&openid=' . $token['openid'] . '&lang=zh_CN';
        $resp     = @ihttp_get($userinfo_url);
        $arr      = @json_decode($resp['content'], true);

        return $arr;
    }
}