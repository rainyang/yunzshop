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

	}

	public function login()
	{
		$uniacid = \YunShop::app()->uniacid;
        $appId = \YunShop::app()->app_id;
        $code = \YunShop::request()->auth_code;
        $state = \YunShop::request()->state;

		$callback = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];



		if (empty($code)) {

			$alipay_redirect = $this->__CreateOauthUrlForCode('2016091400510967', $callback);

			redirect($alipay_redirect)->send();
			exit();
		}



	
        $aop = new AopClient;
		// $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
		$aop->gatewayUrl =  'https://openapi.alipaydev.com/gateway.do';
		$aop->appId = 2016091400510967;
		$aop->rsaPrivateKey = 'MIIEpQIBAAKCAQEArbnGT/3yudWrDpGi3CSNmEXLRtqJ00IpiqgzIlXhWhDim7A2DGtm4DZBZPPpf092C3pHCgiGT1CsSJN8A5HKS3z0s5OTVMV7PFh8g8Q6PG34ky877YBVOJfxnPITq5N75NaZBsyopREC86YF5dSAhxEGtuZsVrFhdnrss3uEof/vXtjRti7bYCW59TbsGVcF6pCMFXNZnatHF03Sn4fdshEsJBB1ovPSLzr3J0fZSuR/yZceILSf1Ae9eqoU6J90SdxEPKDsr99LUCCdQDI5d09XfpeRFubTriGFRQcXwtQG4XqCdCiC1E4RV2STZdCEtu/+IvPP2Hl0w4Q+W4rfkwIDAQABAoIBAQCT7DpTBF24JJpZdmJg5nL2dH9I3m46xvxORzf7BXsz0GrTvC/ZVV4sjaYTCqkIMSXHUh/JCEcso/l+dq5C79F7g3NssLzjkp+DW6jyHZSkvdLJqI9zhDGMVbxyMaHUF2mICb4VDxCONLT5t6UP9GhYGYRJGlynWLQvSS3hCcx+juC1kGL2qCbhYMEipWFexDj+mX7wYPU427DUr+J/5ghUEtr1zWyMgj2ayh7aVwAQRR5USz7zHFOAImrUI+iY9TjhFxKHMTGvWHyyQ9PRBk7daRwkbw/XMxvKrssh1F9qTSHftPJZU5PqUewfqdFIjeIWxCwQ8E1fJUrF1Yn8qfbhAoGBAOacc1ZBEPxpE+VL2p/bbt1pjz6PjKbXqFqHaiqz+ZM+M1/kHrebPxoTmTgRxH+RxJKC60EwODlWzYWCL7zIUeA/r1LBqng9zZ2f5jLNd55cji7dg0UxA9pwAe9Fae7CWEboQ3mRt3nhvzJS5Gdm+PO0sWrOBWCxH3BR21xpGj5RAoGBAMDaEDATCwuzXQl0FS+VJob8VcHpAu1uUzeTg8eOl0behZs3AFoi2f4dM7hIfHjMTyAmeSDGLG1U6rYwvIfAFZCY/vRllNKk6rdQclHMwmVlAk3gtTj0aTzrXsMdRlk3/Y3mpAfLyKy6i22vAvo2zhXoWK8i8Y1klVugIolZ25KjAoGACMeGAI+jlOhvMHiNzy4sb1N4d5pBcYyeRSwIl62YX8mrbQgjPzwyz8xVNfQ/NdgX0rgXOrkaOtyaIG4PYTGDJHVoOE9VLRS1Qj33JpH57tZ5N0GJoNW33tYKzNAos0VE1RY8k2E/ye0VOVY8ic86xRRo+Lb1L3QuzTjgkAkUTbECgYEAvNgMB/dt1oxR64LniAumiYIou9RfUH67MG3cGhqQGg6miEJIrvr3ujWAQtGASY3PYa0q6aQFNx5zI+r/gnLe6xRWfJ3IKKjjSpSVDXLTXRSXOAl1jAsRzlyxE6DkFVOVj1GtiBDONg4JoGNDKB9omN1HkmBGZu16sBlUGGmMcL8CgYEAqfWa9T0G1c4oNjBcOvxVt6V6vkACVTtg5gfLy5I+s8VEybWPdSXzsWeILh/hRYdcanXqGQ2nFlknfH3e4m+6C1jcXUkJ/5wAAC3k0CfWoaa3G3VPinlGIbtI3tZ0Xe4/Pb3cl4UEpkOYHG4Wtl/Ghzur06wxveO76lhTZGeOrP8=';
		
		$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArbnGT/3yudWrDpGi3CSNmEXLRtqJ00IpiqgzIlXhWhDim7A2DGtm4DZBZPPpf092C3pHCgiGT1CsSJN8A5HKS3z0s5OTVMV7PFh8g8Q6PG34ky877YBVOJfxnPITq5N75NaZBsyopREC86YF5dSAhxEGtuZsVrFhdnrss3uEof/vXtjRti7bYCW59TbsGVcF6pCMFXNZnatHF03Sn4fdshEsJBB1ovPSLzr3J0fZSuR/yZceILSf1Ae9eqoU6J90SdxEPKDsr99LUCCdQDI5d09XfpeRFubTriGFRQcXwtQG4XqCdCiC1E4RV2STZdCEtu/+IvPP2Hl0w4Q+W4rfkwIDAQAB';
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
			//打印user信息
			echo "<pre>";
			print_r($user);die;
		} catch (Exception $e) {
			echo "<pre>";
			print_r($user);die;
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
        return "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?appid=".$appId."&scope=auth_user&state={$state}&redirect_uri=".urlencode($callback);
        //https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=APPID&scope=SCOPE&redirect_uri=ENCODED_URL
    }
}
