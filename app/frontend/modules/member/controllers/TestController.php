<?php



namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\common\services\alipay\request\AlipayUserUserinfoShareRequest;
use app\common\services\alipay\request\AlipaySystemOauthTokenRequest;
use app\common\services\alipay\AopClient;
use app\common\helpers\Url;

/**
* 
*/
class TestController extends BaseController
{

	//member.test.img
	public function img()
	{
		$redirect_uri = 'https://release.yunzshop.com/authorize.php?i=6&route=member.login.index&type=8&session_id=e4c22a92ea591b5c9895250e47016bbd&mid=0&yz_redirect=aHR0cHM6Ly9yZWxlYXNlLnl1bnpzaG9wLmNvbS9hZGRvbnMveXVuX3Nob3AvP21lbnUjL21lbWJlcj9pPTY=&midundefined';
 		// \QrCode::format('png')->size(320)->generate($redirect_uri);
		return \QrCode::size(320)->generate($redirect_uri); 
	}


	public function abcd()
	{
        $appid = '2016091400511024';
        $redirect_uri = Url::shopSchemeUrl('payment/alipay/returnUrl.php');
		$url ="https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={$appid}&scope=auth_userinfo&redirect_uri={$redirect_uri}";
		// dd(1);
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
		$aop->appId = '2016091400511024';
		$aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEAxOtHgweGzvdHX1T0I/OnLHmmr/HJZIPIeMy08hVamxcm6KjLR/3P5ZGbZKBQeIK8yde7ccf6VhJKjrH9ALCQKgipElXeG3lY+NLQstHIjwtlwn3VBvjOBv26D9fPLm5ThJGBxJQ+ndDK5uqP5kxktd+lY2gom9MsaRhfe/ykn5egdRkPV5h7wcPybPdflJXT+tucRQ6OLbNwvW8yGkX8Z4vR3GLMDijoMzntQ7JCPIf4lsGe4A6g0Y7WJUEoyJwZgC8qnIDFhJMiyE85JgmuTJTmrwsqdDjTxspgpzNC3/HolUUWrOd58EjZnu/ffIwLBnnJMWm1DE3oMBLiPRpEJQIDAQABAoIBAFuhoAU422IzbuLNhU21c3UeppH40N4U9JRBrXF4vlCs1U4uPWmikbshpk2My+VH8NF7sZ2gkLy8hjUgXbqUboEgxovhqRjfvqcKclLDi7AEfbWjGB3GaRiXuJzmr2HLtNFbZCc1VG3bWo9ZVtyzb6myCCPZtAvOmDvPO59WUMRx2Hz47zlBQ9YjrMEknh9TOxQTflI2cBq//LkVTWUT9lOfUxX3rSZ6iDGwKhmbKptZfDk2pwVi0wKf++P8+VWeCTHBDe0STZCfsGI+uJIYtKwYjLI5AlxVDKVS52mXowVuI/YcLStiRZaGquWoljjtSDWE/Ic1GNSzUlrVeRDXCAECgYEA8vaVCKv4fYoAvEEtaUIc0tx8UqSZERG9cxXe3W9jeP6KHUGcqTaydH8l1ZrAwojVDK/OQt2kVzX16WZ/r03KCZhmReWrVMFMW1AyAVQvSciLEPEgTl1eetc0BwVIx9JT2oq0SKnYMqyCAM+DUbdpq87QKOUa0VoA4B/SNLsCuJUCgYEAz3w4cJ/vh9OAkXaW7fYwQP16Q6tMXpw6j9hNY301BRezNec96rblAKER+pD90fsecUCDVzUaTQ3fUS19RCLIq5NZr2hkqa/S/kCmh3XWkzdegy71+B2kAnxZzXB/MXl182ljpexdkxPbyanr6rOk6hLACgi8ODsypaC1GBl2KVECgYEA3/AA25ZOGb+5/8n/RhYmpP+OI0oMvxvfxxRwbx68y4eo22BfOePtRczvnVresi8WV3QvI5hQYgWdW/waUgTb5E4wbdpLOXpUm5FmFa13TIVoEDx/L1uFuqliDdqEA5FLspHHq3XIjKRNytt5STdJUY70c8z/E9jmF62cvPdlgaUCgYEAs7A73OXV9sanwNJ4Uat1DMO5ACO45vLcELleBDxqD3f//z29tKkiWFImFN8+wnx9V81sMblOhs7tyQrhQoHxUg4xjKSXERGxY/ovfn8CDsrT8j5YTMG2yWRSDYZQ0VMEYIK0Bv1V6Ms4/ERJiSB9QS5t3ALGptg1u5UbYLykRZECgYEAtTSAkY/UROU879/VaNdysJ0YX9VUY3rjCXuqjS/hdMijC438k5ojNQkSGGu53d23JusqrGJMQPwEVERV41cAopnceki5OH7xpP1pi4GkHeSTny99qMzzB6RgjrAXCoT5I7E5CPbHc8QsedK0cMBtAuQujTdETOJZE3fTMvscHzY=';
		$aop->format = "json";
		$aop->postCharset = "UTF-8";
		$aop->signType= "RSA2";
		$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxOtHgweGzvdHX1T0I/OnLHmmr/HJZIPIeMy08hVamxcm6KjLR/3P5ZGbZKBQeIK8yde7ccf6VhJKjrH9ALCQKgipElXeG3lY+NLQstHIjwtlwn3VBvjOBv26D9fPLm5ThJGBxJQ+ndDK5uqP5kxktd+lY2gom9MsaRhfe/ykn5egdRkPV5h7wcPybPdflJXT+tucRQ6OLbNwvW8yGkX8Z4vR3GLMDijoMzntQ7JCPIf4lsGe4A6g0Y7WJUEoyJwZgC8qnIDFhJMiyE85JgmuTJTmrwsqdDjTxspgpzNC3/HolUUWrOd58EjZnu/ffIwLBnnJMWm1DE3oMBLiPRpEJQIDAQAB';

		if (!isset($code)) {
			$this->abcd();
		}

\Log::debug('dasd'.\YunShop::request()->auth_code);
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

		echo "<pre>";
		var_dump($user_id);
		var_dump($headimgurl);
		var_dump($nick_name);
		print_r($result_a->$responseNode_a);die;
	}
}