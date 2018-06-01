<?php

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\common\services\alipay\request\AlipayUserUserinfoShareRequest;
use app\common\services\alipay\request\AlipaySystemOauthTokenRequest;
use app\common\services\alipay\AopClient;
use app\common\helpers\Url;


class MemberAlipayService
{
	const LOGIN_TYPE = 8;
	
	public function __construct(argument)
	{
		# code...
	}

	public function abcd()
	{
          
        $redirect_uri = \Url::absoluteApi('member.services.memberAlipayService.login');
		$url ="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={$appid}&scope=auth_userinfo&redirect_uri={$redirect_uri}";

		header("Location:".$url);
		exit;
      
	}

	public function login()
	{
		$uniacid  = \YunShop::app()->uniacid;
        // $appId    = \YunShop::app()->app_id;
        $code     = \YunShop::request()->auth_code;

		$aop = new AopClient;
		// $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
		$aop->gatewayUrl =  'https://openapi.alipaydev.com/gateway.do';
		$aop->appId = 2016091400511024;
		$aop->rsaPrivateKey = '';
		$aop->format = "json";
		$aop->postCharset = "UTF-8";
		$aop->signType= "RSA2";
		$aop->alipayrsaPublicKey = '';

		if (!isset($code)) {
			$this->abcd();
		}


		$request = new AlipaySystemOauthTokenRequest ();
		$request->setGrantType("authorization_code");
		$request->setCode($code);//这里传入 code
		$result = $aop->execute($request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$access_token = $result->$responseNode->access_token;

		//获取用户信息
		$request_a = new AlipayUserUserinfoShareRequest ();
		$result_a = $aop->execute ($request_a,$access_token); //这里传入获取的access_token
		$responseNode_a = str_replace(".", "_", $request_a->getApiMethodName()) . "_response";

		$user_id = $result_a->$responseNode_a->user_id;   //用户唯一id
		$headimgurl = $result_a->$responseNode_a->avatar;   //用户头像
		$nick_name = $result_a->$responseNode_a->nick_name;    //用户昵称

		var_dump($result_a);
	}


	 /**
    //  * 构造获取token的url连接
    //  * @param string $redirectUrl 微信服务器回跳的url，需要url编码
    //  * @return 返回构造好的url
    //  */
    // private function __CreateOauthUrlForCode($redirectUrl)
    // {
    //     $urlObj["app_id"] = $this->appId;
    //     $urlObj["redirect_uri"] = "$redirectUrl";
    //     $urlObj["scope"] = $this->scope;
    //     $urlObj["state"] = "STATE"."#alipay_redirect";
    //     $bizString = $this->ToUrlParams($urlObj);
    //     return "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?".$bizString;
    // }
}
