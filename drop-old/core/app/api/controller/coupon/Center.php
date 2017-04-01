<?php
namespace app\api\controller\coupon;
@session_start();
use app\api\YZ;

class Center extends YZ    //优惠券列表
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callPlugin('coupon/index');
        $this->variable = $result['variable'];
        $this->json = $result['json'];

    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}

//array('list','pagesize')
//循环遍历list之后变量(字段名):
//优惠券名称:couponname
//优惠券时限：timestr       注；此变量为空时，状态则为“永久使用”， 不为空时则，有效期：timestr
//优惠券过期：past        如果此值为false，则代表此优惠券已过期，可与优惠券时限变量结合着判断
//优惠券颜色：css
//优惠券类型：backstr    如：立减 折扣
//优惠券金额：_backmoney
//优惠券领取条件: getstatus   值为0时，需要支付积分和余额，  值为1时，需要支付余额，值为2时，需要支付积分，值为3时，免费领取
//支付的余额：money
//支付的积分：credit
//优惠券每人限领：getmax   值为-1时，不限领取张数，为0时，不可以领取，大于0不等于-1时，  则显示：每人限getmax张