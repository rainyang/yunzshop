<?php
namespace app\api\controller\member;
@session_start();
use app\api\model\Member;
use app\api\YZ;
use app\api\Request;

class Login extends YZ
{
    public function index()
    {
        /*$validate_messages = $this->_validatePara();
        if (!empty($validate_messages)) {
            $this->returnError($validate_messages);
        }*/
        $para = $this->getPara();
        $info = $this->_getUserInfo($para);
        if (empty($info)) {
            $this->returnError('用户名或密码错误');
        }
        $this->_setCookie($info['openid'],$info['mobile']);
        $this->returnSuccess($info);
    }
    private function _getUserInfo($para){
        $info = D('Member')->field('id,openid')->where($para)->find();
        $member  = m('member')->getMember($info['openid']);

        if(!empty($info)){
            if(p("bonus")){
                $member['commission_level'] = p("bonus")->getLevel($info['openid'])?:'普通等级';
            }else{
                $member['commission_level'] = '普通等级';
            }
        }
        return $member;
    }
    private function _validatePara(){
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '公众号id'
            ),
        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
    private function _setCookie($openid,$mobile){
        global $_W;
        if (is_app()) {
            $lifeTime = 24 * 3600 * 3 * 100;
        } else {
            $lifeTime = 24 * 3600 * 3;
        }
        session_set_cookie_params($lifeTime);
        $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";
        if (is_app()) {
            setcookie($cookieid, base64_encode($openid), time()+3600*24*7,'/');
        } else {
            setcookie($cookieid, base64_encode($openid),0,'/');
        };
        setcookie('member_mobile', $mobile);
    }

    /**
     * 小程序登陆
     */
    public function wx_app_login()
    {
        load()->func('communication');

        $para = $this->getPara();


        $data = array(
            'appid' => 'wx31002d5db09a6719',
            'secret' => '9e2d6dbafb37b40c9413d2966e1a3dea',
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';

        $res = ihttp_request($url, $data);

        echo '<pre>';print_r($res);exit;

    }
}