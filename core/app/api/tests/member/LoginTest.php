<?php
namespace test\api\controller\account;
use \app\api\controller\member;
require_once __CORE_PATH__;

class LoginTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {

    }

    public function testLoginSuccess() {
        $para = array(
            "mobile"=>'18545571024'
        );
        $out = $this->get('member/Login',$para);
        $this->assertEquals($out['code'], 0);
    }
    public function tearDown(){
        
    }
}


