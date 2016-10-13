<?php
namespace test\app\api\order;
use tests\app\api\TestCase;

require_once __DIR__.'/../TestCase.php';
class DetailTest extends TestCase  {
    public function setUp() {
        parent::setUp();
    }

    public function testIndex() {
        $para = array(
            "order_id"=>'906',
        );
        $out = $this->get('order/Detail',$para);
        $this->assertEquals($out['result'], '1');
    }

    public function tearDown(){
        
    }
}


