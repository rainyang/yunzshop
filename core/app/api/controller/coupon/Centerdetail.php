<?php
namespace app\api\controller\coupon;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Centerdetail extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callPlugin('coupon/detail');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}
//数组内有三个数组或值，coupon, set
//coupon数组内的字段：
    //优惠券名称:couponname
    //优惠券时限：timestr       注；此变量为空时，状态则为“永久使用”， 不为空时则，有效期：timestr
    //优惠券过期：past        如果此值为false，则代表此优惠券已过期，可与优惠券时限变量结合着判断
    //优惠券颜色：css
    //优惠券类型: coupontype  值为1时是充值 ，否则为消费
    //优惠券条件：enough   值为空时是任意金额，否则为 ：满enough可用 。例如：满100可用
    //优惠券优惠方式：backtype  值为0时为立减，为1时为折扣，为2时为奖励余额 现金 或者 现金
    //优惠券立减金额：deduct
    //优惠券折扣: discount
    //注：此三项需要加判断，不为空时显示变量
    //优惠券奖励余额: backmoney
    //优惠券奖励积分: backcredit
    //优惠券奖励现金: backredpack
    //优惠券使用说明: desc
    //优惠券是否使用统一说明 ：descnoset 值为空时则显示统一说明 不为空则不显示
    //优惠券统一说明：数组set   此为上面三个数组或值中的第二个，输出此值时，判断优惠券类型（coupontype） ,值为空时输出 set['consumedesc'] ,不为空时输出set['chargedesc']
    //优惠券获取方式: getstatus   值为0时，需要支付积分和余额，  值为1时，需要支付余额，值为2时，需要支付积分，值为3时，免费领取
    //支付的余额：money
    //支付的积分：credit
    //领取的文本：gettypestr
    //最多领取张数：cangetmax  值为-1时，不限制领取张数，
    //判断是否领取过 canget  如果cangetmax不为-1，需要判断此值，为true时，显示：'您还可以' .gettypestr. .cangetmax.张！false时，显示您已经领取过

