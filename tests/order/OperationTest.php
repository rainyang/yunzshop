<?php
namespace test\app\api\member;
use tests\app\api\TestCase;

require_once __DIR__.'/../TestCase.php';
class LoginTest extends TestCase  {
    public function setUp() {
        parent::setUp();
    }

    public function testLoginSuccess() {
        $para = array(
            "mobile"=>'18545571024',
            "pwd"=>'sgl918'
        );
        $out = $this->get('member/Login',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testPasswordError() {
        $para = array(
            "mobile"=>'18545571024',
            "pwd"=>'111111'
        );
        $out = $this->get('member/Login',$para);
        $this->assertEquals($out['result'], '0');
    }
    public function tearDown(){
        
    }
}


