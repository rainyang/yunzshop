<?php
namespace app\frontend\modules\dispatch\controllers;
use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\modules\order\models\Order;
use Ixudra\Curl\Facades\Curl;
use \Request;
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:03
 */
class ExpressController extends ApiController
{
    public function index(Request $request)
    {
        $order_id = $request->query('order_id');
        $order = Order::find($order_id);
        if(!isset($order)){
            throw new AppException('未找到订单');
        }
        if(!isset($order->express)){
            throw new AppException('未找到配送信息');
        }
        $data = $this->getExpress($order->express->express_code,$order->express->express_sn);
        $this->successJson('成功',$data);
    }
    private function getExpress($express, $expresssn)
    {
        $url = sprintf('https://m.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=%s', $express, $expresssn, time());
        //$url = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$express}&fromWeb=null&postid={$expresssn}";
        //\load()->func('communication');

        $result = Curl::to($url)
            ->asJsonResponse(true)->get();
        if (empty($result)) {
            return array();
        }
        return $result['data'];
    }
}