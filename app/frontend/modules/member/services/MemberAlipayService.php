<?php

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\common\services\alipay\request\AlipaySystemOauthTokenRequest;
use app\common\services\alipay\request\AlipayUserInfoShareRequest;
// use app\common\services\alipay\request\AlipayUserInfoAuthRequest;
use app\common\services\alipay\AopClient;
use app\common\helpers\Url;


class MemberAlipayService extends MemberService
{
	const LOGIN_TYPE = 8;
	

    private $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm";  
    //支付宝网关
    private $alipay_api = "https://openapi.alipay.com/gateway.do"; 

    private $aop; 

    public function __construct()
    {
    	// parent::__construct();

    	$this->aop = $this->aopClient();
    }

	public function login()
	{
		$uniacid = \YunShop::app()->uniacid;
        $appId = \YunShop::app()->app_id;
        $code = \YunShop::request()->auth_code;

        //回调域名
		$host = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'];
        $callback = urlencode($host.$_SERVER['PHP_SELF']);
        //回调地址
        if($_SERVER['QUERY_STRING']) $callback = $callback.'?'.$_SERVER['QUERY_STRING'];

		if (empty($code)) {

			$alipay_redirect = $this->__CreateOauthUrlForCode($this->appid, $callback);

			redirect($alipay_redirect)->send();
			exit();
		}
		$request = new AlipaySystemOauthTokenRequest();
		$request->setGrantType("authorization_code");
		$request->setCode($code);//这里传入 code
		$result = $this->aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$result = json_decode($result, true);
		$userInfo = $result[$responseNode];

		if ($userInfo = $result[$responseNode] ) {

			//第一步获取到支付宝用户user_id,判断商城是否已存在这个用户

			//第二步用户已存在则直接登录，不存在则 通过 access_token 获取用户信息添加到支付宝用户表

			$user = $this->getUserInfo($userInfo['access_token']);
			dd($user);
			/*alipay_system_oauth_token_response" => array:6 [
    			"access_token" => "authusrB6e094cbc0cd54ed18e69b35e2000aX41"
    			"alipay_user_id" => "20880051464321899646564260914141"
    			"expires_in" => 1296000
    			"re_expires_in" => 2592000
    			"refresh_token" => "authusrB070ef1be4ccb4c98abd97ca781faaX41"
    			"user_id" => "2088212325598416
    		]*/

		} else {
			/*error_response" => array:4 [
    			"code" => "40002"
    			"msg" => "Invalid Arguments"
    			"sub_code" => "isv.code-invalid"
    			"sub_msg" => "授权码code无效"
  			]*/
  			\Log::debug('支付宝授权失败code:'.$result['error_response']['code']);
			return show_json(-3, '支付宝授权失败');
		}

	}

	private function aopClient()
	{
		$alipay_set = \Setting::get('plugin.alipay_onekey_login');
		$aop = new AopClient;
		$aop->gatewayUrl = $this->alipay_api;
		$aop->appId = $alipay_set['alipay_appid'];
		$aop->rsaPrivateKey = $alipay_set['private_key'];
		$aop->alipayrsaPublicKey = $alipay_set['alipay_public_key'];
		$aop->signType=  $alipay_set['rsa'] == 1 ? 'RSA' : 'RSA2';
		$aop->apiVersion = '1.0';
		$aop->format = "json";
		$aop->postCharset = "UTF-8";

		return $aop;
	}

	/**
	* 根据access_token 获取用户信息
	* @param 
	* @return 返回用户信息
	*/
	protected function getUserInfo($access_token)
	{
		$request = new AlipayUserInfoShareRequest();
		$info = $this->aop->execute ($request,$access_token); //这里传入获取的access_token
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$json_info = json_decode($info, true);

		if (!empty($json_info['error_response'])) {
			\Log::debug('支付宝获取用户信息失败code:'.$json_info['error_response']['code']);
            return show_json(-3, '支付宝授权失败');
		}

		return $json_info[$responseNode];
	}


	 /**
     * 构造获取token的url连接
     * @param string $callback 支付宝服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($appId, $callback, $state = 'info')
    {
        //return $this->url."?app_id=".$appId."&scope=auth_userinfo&redirect_uri=".$callback."&state=".$state;
        return $this->url."?app_id=".$appId."&scope=auth_user&redirect_uri=".$callback."&state=".$state;
    }

}
