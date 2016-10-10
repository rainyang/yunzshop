<?php
namespace test\app\api\order;
use tests\app\api\TestCase;

require_once __DIR__.'/../TestCase.php';
class DisplayTest extends TestCase  {
    public function setUp() {
        parent::setUp();
    }

    public function testAll() {
        $para = array(
            "order_id"=>'0',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testWaitPay() {
        $para = array(
            "order_id"=>'',
            "status"=>'0'
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testWaitSent() {
        $para = array(
            "order_id"=>'',
            "status"=>'1'
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testWaitDelivery() {
        $para = array(
            "order_id"=>'2',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }

    public function testFinish() {
        $para = array(
            "order_id"=>'3',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testClose() {
        $para = array(
            "order_id"=>'-1',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testApplyRefund() {
        $para = array(
            "order_id"=>'4',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function testRefund() {
        $para = array(
            "order_id"=>'5',
            "status"=>''
        );
        $out = $this->get('order/Display',$para);
        $this->assertEquals($out['result'], '1');
    }
    public function tearDown(){
        
    }
}


