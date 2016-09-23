<?php
namespace app\api\controller\member;
@session_start();
use app\api\Request;
use app\api\YZ;

class SentCode extends YZ
{
    public function index()
    {
        $validate_messages = $this->_validatePara();
        if (!empty($validate_messages)) {
            $this->returnError($validate_messages);
        }
        $para = $this->getPara();

        if (D('Member')->has($para)) {
            $this->returnError('该手机号已被注册！不能获取验证码。');
        }

        $mobile = $para['mobile'];

        $code = rand(1000, 9999);
        $_SESSION['codetime'] = time();
        $_SESSION['code'] = $code;
        $_SESSION['code_mobile'] = $mobile;
        //$content = "您的验证码是：". $code ."。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";
        $issendsms = $this->sendSms($mobile, $code);
        //print_r($issendsms);

        $set = m('common')->getSysset();
        //互亿无线
        if ($set['sms']['type'] == 1) {
            if ($issendsms['SubmitResult']['code'] == 2) {
                $this->returnSuccess();
            } else {
                $this->returnError($issendsms['SubmitResult']['msg']);
            }
        } else {
            if (isset($issendsms['result']['success'])) {
                $this->returnSuccess();
            } else {
                $this->returnError($issendsms['msg']);
            }
        }
    }

    private function _validatePara()
    {
        $validate_fields = array(
            'mobile' => array(
                'type' => 'required',
                'describe' => '手机号'
            ),
            'uniacid' => array(
                'type' => 'required',
                'describe' => '公众号id'
            ),
        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}