<?php
namespace app\api\controller\member;
@session_start();
use app\api\Request;
use app\api\YZ;

class ForgetPassword extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        //member/ForgetPassword&mobile=18545571024&password=111111

        global $_W,$_GPC;
        if(($_SESSION['codetime']+60*5) < time()){
            $this->returnError('验证码已过期,请重新获取');
        }
        if($_SESSION['code'] != $_GPC['code']){
            $this->returnError('验证码错误,请重新获取');
        }
        $_W['ispost'] = true;
        $_GPC['memberdata'] = array_part('mobile,password',$_GPC);
        $result = $this->callMobile('member/forget/sendcode');
        $this->returnSuccess($result['json']);
    }
    public function sendCode()
    {
        //member/ForgetPassword/sendCode&mobile=18545571024
        $result = $this->callMobile('member/sendcode/forgetcode');
        $this->returnSuccess($result['json']);
    }
}