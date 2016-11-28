<?php
namespace app\api\controller\coupon;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class My extends YZ    //优惠券列表
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callPlugin('coupon/my');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        if(empty($this->json['list'])){
            return $this->returnError('该账户没有优惠券');
        }
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