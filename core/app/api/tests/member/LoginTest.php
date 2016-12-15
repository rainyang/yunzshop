<?php
namespace test\api\controller\account;
use app\api\tests\TestCase;
require_once __DIR__.'/../TestCase.php';
class LoginTest extends TestCase   {
    public function setUp() {

    }

    public function testLoginSuccess() {
        $para = array(
            "mobile"=>'18545571024',
            "pwd"=>'sgl918'
        );
        $out = $this->get('member/Login',$para);
        $this->assertEquals($out['code'], 11);
    }
    public function tearDown(){
        
    }
}


