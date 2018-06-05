<?php

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\common\services\alipay\request\AlipaySystemOauthTokenRequest;
use app\common\services\alipay\request\AlipayUserInfoShareRequest;
// use app\common\services\alipay\request\AlipayUserInfoAuthRequest;
use app\common\services\alipay\AopClient;
use app\common\helpers\Url;


class MemberAlipayService
{
	const LOGIN_TYPE = 8;
	

	//沙盒环境参数  
    private $appid = '2018060460281631';  
    private $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm";  
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
		\Log::debug('支付宝：'. $code);
     

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
			return show_json(-3, '支付宝授权失败code:'.$result['error_response']['code']);
		}

	}

	private function aopClient()
	{
		$aop = new AopClient;
		$aop->gatewayUrl = $this->alipay_api;
		$aop->appId = $this->appid;
		$aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEAyVAeILGJrFsceHzoEygk/bbSGvvNnvnbD28TKNXju0oV9/0uuFFgbUrG+C5nc1hpGxo/CqiaETcA2zG0VlRu7XB3cCnvADs6rrntTWrZdoGR91+lEOXjnBtHv5J8TNqYXvXpyMxfWTgQcZofpzA0g01bXZfQ5DsrSD+yeFpRmCxzB8F2ndcXfuw4A5bn4cuBmpmCGCIx8FG+5x+EUucodx3nuNm+Jh3jp5LXvhK4OfCd03yNoDpQbzXXdPI2WBAE0eMB3Pt0PinOZEIaxL9MS1Y/ZOv+nubzbkVdhRc8sz645xcXFrUf3KdvsOZ3LexFTRTxF+4wcX+AWbuWSecMcQIDAQABAoIBAQCYF2B7oMX7omYzHWMUPgscZ8f6vOyPRANdeLSH8Hh6IjHQxsZKWKi6SXljPWPJAC2AXWbtfY3QnbaW48l0Q5v+5S5HXlcD3LusECoZiDU9VAzcULVbu+MnKHEfaeNhCPF/JNj4bHdI55N80E1Duaai4Im7fxxBofZEQmNqjAoDJZIEBFi5CB7SrPFrUZ6OQ2MnxPauBZab7m/fnc5uG2l0Le4fIKAjl0Obe0jS9258oEdUDmgyyNw8aDIFrUEAsMw0h/YON0AV68Yyjz3cXETqv+z9+FHMxMahgZQmyKNC4GMI3hXJNxVYYtOEBCjQW+oiUxYax7oM/vUJiwNZUNhBAoGBAPK0ihWVK8f98DdAofoTMSFSSSFpTb8BQzCE0xazrprkDYa7UXQJQoL6Q48rc1qZvz5V4XupZGdndTLWROQspZA1CIpvw4CL6Jb0gT1tM+t9wvkDxYH8Q8L8H2sqCWsEBSf/FtuyqxWdfilLkM9w09whioigMfoRbwws8aWuhML5AoGBANRXIjKKP+GvvGmISId0iSF7g/MeMAyIO9Ldd+/BU06GKCKWyHu583hySmVa1pPgxuu0Mh2gpotkySVkuemWyit0od5kt4dsRP2ENdDjfWlcIMn7Lx9fNTQLAkHqUL0igpuwuAOjIj/61tbbQwb9UAfvYJE7RMsiElXs/yYKens5AoGBAMumX8NSYuUyF/FUw1VB61SpZgGqCXl/BrDckv8WkCkZuJvX67Xw2yVp92xXqjhYj9cvWr9X2I7Hidi5YB8Rs264gU0gEKx5ORYJXbR8QDeWVBZ8aqryUK14vqg+Ip7wRZ9U9Qot9k5x0121MXJOmwa4AjU4LhdFr6dIww8hy/aJAoGBALAiHtHBb7/rH+SCEXeaqO1HIWqXDdA3aTg+UPBlco7eJYibfm1zD4xHcYKlWPyNJTP64t9ElSFnVppX9QbX95cYRfTNopcIrimEc4d0TGEK9H/WhX4GYYFr6FF45cQdTi2K5vjNZumfTnomonC3ypzqaTXO7f95oa/4yKRraLGxAoGAH/MKQFVRkf8PotD7xrlkPA/cr9DMijBQWmSMc+bT4QD+F9lnVWtQ5XnPP3xpiU6Po0beg3sLRN20QJ/t7KepNkgBCF7U4VHOLJh2YWxCrJk35dorBV1Ma/ZjsmJE9LAcmELY0UmIKVGXq9kyv2Gh3oM6zwPOabzj4ptHawYFEP4=';
		
		$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2fvYacITAN1BKRLlH3ejxiDWJ+2JZfcGl720BZzJW/7BaZuNUrAH3YJE18py9WcAS49fFlBC238yBEumQ4orNwDx8r5oJWxok1KsLKZSA+/gmssxVTdn9jaVkK1XjyPT+fVlvsl5AyMY2+7If4mSAbIL8ghHNVKtdqrDgLQ6Stz8iSa2/Upn+ZlvO322wqQdWcaj4xPVkzGOcS2J+X8uXZZ6aCzgmRXtLUHNTcXAnevcTSqmWCVeKFFDHQYlAccs2owWsUKgiblMhCT2d2n6QVoaTyWk6pgyNip4IfmH7kGkwJ6ycweD6xIFBRnaileR4tC9hgVWVBjEMhNAOaDeAwIDAQAB';
		$aop->apiVersion = '1.0';
		$aop->format = "json";
		$aop->postCharset = "UTF-8";
		$aop->signType= "RSA2";

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
