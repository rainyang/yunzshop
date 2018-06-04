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
	

	//沙盒环境参数  
    private $appid = '2016091400511024';  
    private $url = "https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm";  
    private $alipay_api = "https://openapi.alipaydev.com/gateway.do";  

	public function login()
	{
		$uniacid = \YunShop::app()->uniacid;
        $appId = \YunShop::app()->app_id;
        $code = \YunShop::request()->auth_code;
        $state = \YunShop::request()->state;

		$callback = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

\Log::debug('支付宝'. $code);


		if (empty($code)) {

			$alipay_redirect = $this->__CreateOauthUrlForCode($this->appid, $callback);

			redirect($alipay_redirect)->send();
			exit();
		} else {
	
	        $aop = new AopClient;
			// $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
			$aop->gatewayUrl = $this->alipay_api;
			$aop->appId = $this->appid;
			$aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEAyvx6vd75iLI1KCoc8KaJ9JugOlGNgMo+Wmy2Lo1fKCxSEeZgX99Uz2lt0tynJyMZDymUnjNg426iLHCQ8SfWO1Vr0CliDMNAYc3uUxMehAKklRxbZZktJdU/YwN3iOu0Q1vlC1vfhU/GWBBUevTd+CySudSJQWGXCgn+6SteYtAZOZVGYExaeEPHVNxzYIrNRCQB3Kjn0ec1uvsKtVxRj6bfXFiNRBQHe9AbonBO++v7ty2nVrmeSQWxpuAfgZ5+GcNAo0T4fqtQLVX+HTKXc3QIp5ecnMTQznmUhqT+PezeNRyfjWannrZIuhfdqxZAbdrarCviPn3luK+80vlA9QIDAQABAoIBAQCaWxxLPj+q/zkE7eFL7piBdcaGEnX0NdbslDaFd+OgfPN7wSAQR5gKkTV+X2SMklf/+7KUCqXmzL5t5LuTZqO2QuLVTGLPKbrPpPVSHvvZjtjwuruVqsF2P48QEBbZ+8L8ZejqllaG3X8KgIB9b69LhTmeLkyhd0CP1cIONXh00kdZ6zZIYmczAy+Hm+yxFoxIZm2OcORMMyWRQICWdaSVzHpZAhiiTZWooIhPlVo9NFxH5Z2EzSBFJqF8sJBPWVG/onE4qM85cCd/QaGCwTbXGz8Xt/TXjXsV+koQmkyrbcWWP2/LhY7ZdRcvW+qLu9Tk7JawgClFX9P+/tXhQBIRAoGBAOhrf9UwC7JFXdo3MIAK6PiNdv8js0bIphXCGYRp0fIql5qRYgkL/bzXdyPYgNmv+U19iPWXfWOl6HhL8/QRQ1DphFzT1X1sVnwLjpDvAVC02+yAzBQeKWsgqxCXfwwrxmZuxvsFcHeEgAJIZb4oQwNev3u45YVYM1KpE/lUgoVzAoGBAN+UhHApxtAn/uta4prkr+DXiai4bugAUbKTaEPUX+03O8YMgcH7hcLqQlHjd53NkXFSRRm+2hGSX6koM3bJYTD22xY3uT8aTpPSVkSeLq3ex/6hWDfjv/qBqfDXT7Y/oTE3At4HLydVbg7ccbJ9BMEngnWL/S88QYbM2yCYA8X3AoGAV12JWN7Nlr6Cb/OM9KSlPEEY+QE3c6Ua4VTr+J06gPhHsp9xpYrvX1vy+fN5Q9rlMJ6+q+q9BIcp4oZSdm1Cy5hr2+T4/EOMIubJOWvOJ8NEZBtqGynXUeCezQbViAKwenKrs1IxG4wf/juumxNRVWP5QI2ZIU2tRSYvTurYgUcCgYEAz88JMf+CjSM/q530FagNWVy81JdobjctuF+Of807xA6cfj5NtPGFqF94eQiFu6TAVKX1GDLuGXsFcwKsovIWZh5sEECG7AIVmwvbpzenh3AUT7XDe18ypzIxtGtL6cdGmanZ/miLCXI8M4/uFcphyu5gMcWF9It7FEIAQlFI4I0CgYEAopsbgngmsB4bF/LhAKVHY9fqQy9NkZuXLrzicOBLOgLor4Oyj636ihPx2SAXLd70gM0RTPlzY3BhM48qNdpqwqNVeBwA2MDe2G/4IVma5i3r7M/LbsloIIoOH5Mnd/ll4VUdNP9L79e3fxy2DcrBkc88CEkGPs0LA2N5jeh61b4=';
			
			$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6gQ3AdPxUb0blzLqxPpODaLUCkkKVZUrB9jSAZKc08p2qEpoxoviFf/yaAL2LV85iZIlIFl5e1rhNudI3yiCtG2NNgR0+HZGBa2axvClrU2y5SJ/T4qfRppahnR4HLQcuj9f3fq8WCx70N+Vul2x7NsJip+mYMeGR24dzgLvVXQuHGGI8zfaSs3cI7Xt0frJ+AERcpwB2Wu3nQQMeHGcKfoZWS7jx+holmAoAOGSHLa/w+Ab81TlbkbWBjr6mXhylomzkqHF5ygo6loD6nnmV0CgxVBAFOoSwr/xg823Y6axKTSTSY44W05Vz3maxb5OJQ5MukgHiEECCGsPVXYYVwIDAQAB';
			$aop->format = "json";
			$aop->postCharset = "UTF-8";
			$aop->signType= "RSA2";

			$request = new AlipaySystemOauthTokenRequest ();
			$request->setGrantType("authorization_code");
			$request->setCode($code);//这里传入 code
			// $result = $aop->execute($request);
			// $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
			// $access_token = $result->$responseNode->access_token;

			try {
				$result = $aop->execute($request);
				$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
				$access_token = $result->$responseNode->access_token;
				\Log::debug('支付宝'. $access_token);

			} catch (Exception $e) {
				\Log::debug('支付宝'. print_r($e));
			}
		}
		// //获取用户信息
		// $request_a = new AlipayUserUserinfoShareRequest ();
		// $result_a = $aop->execute ($request_a,$access_token); //这里传入获取的access_token
		// $responseNode_a = str_replace(".", "_", $request_a->getApiMethodName()) . "_response";

		// $user_id = $result_a->$responseNode_a->user_id;   //用户唯一id
		// $headimgurl = $result_a->$responseNode_a->avatar;   //用户头像
		// $nick_name = $result_a->$responseNode_a->nick_name;    //用户昵称

		// var_dump($result_a);
	}

	 /**
     * 构造获取token的url连接
     * @param string $callback 支付宝服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($appId, $callback, $state = 'info')
    {
        return $this->url."?appid=".$appId."&scope=auth_user&state={$state}&redirect_uri=".urlencode($callback);

        //https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=APPID&scope=SCOPE&redirect_uri=ENCODED_URL
    }
}
