<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/21
 * Time: 下午4:01
 */

namespace app\backend\modules\order\controllers;


use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\order\Express;

class BatchSendController extends BaseController
{
    public function __construct()
    {
        if (!is_dir(storage_path('app/public/orderexcel'))) {
            mkdir(storage_path('app/public/orderexcel'), 0777);
        }
    }

    public function index()
    {
        $send_data = request()->send;
        if (\Request::isMethod('post')) {
            if ($send_data['excelfile']->isValid()) {
                // 获取文件相关信息
                $originalName = $send_data['excelfile']->getClientOriginalName(); // 文件原名
                $ext = $send_data['excelfile']->getClientOriginalExtension();     // 扩展名
                $realPath = $send_data['excelfile']->getRealPath();   //临时文件的绝对路径

                if (!in_array($ext, ['xls'])) {
                    return $this->message('不是xls文件格式！', Url::absoluteWeb('order.batch-send.index'), 'error');
                }

                $newOriginalName = md5($originalName . str_random(6)) . '.xls';
                \Storage::disk('orderexcel')->put($newOriginalName, file_get_contents($realPath));
                $values = array();
                \Excel::load(storage_path('app/public/orderexcel') . '/' . $newOriginalName, function($reader)use(&$values){
                    $sheet = $reader->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $row = 2;
                    while ($row <= $highestRow)
                    {
                        $rowValue = array();
                        $col = 0;
                        while ($col < $highestColumnCount)
                        {
                            $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                            ++$col;
                        }
                        $values[] = $rowValue;
                        ++$row;
                    }
                });
                $success_num = 0;
                foreach ($values as $rownum => $col) {
                    $order_sn = trim($col[0]);
                    $express_sn = trim($col[1]);
                    if (empty($order_sn)) {
                        continue;
                    }
                    if (empty($express_sn)) {
                        $err_array[] = $order_sn;
                        continue;
                    }
                    $order = Order::select('id', 'order_sn', 'status', 'refund_id')->whereStatus(1)->whereOrderSn($order_sn)->first();
                    if (!$order) {
                        $err_array[] = $order_sn;
                        continue;
                    }
                    $express_model = new Express();
                    $express_model->order_id = $order->id;
                    $express_model->express_company_name = $send_data['express_company_name'];
                    $express_model->express_code = $send_data['express_code'];
                    $express_model->express_sn = $express_sn;
                    $express_model->save();
                    $order->status = 2;
                    $order->save();
                    $success_num += 1;
                }
                return $this->message($success_num . '个订单发货成功,' . count($err_array) . '个订单发货失败[失败原因：订单不满足发货状态]', Url::absoluteWeb('order.batch-send.index'), 'error');
            }
        }

        return view('order.batch_send', [

        ])->render();
    }
}