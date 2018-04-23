<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/21
 * Time: 下午4:01
 */

namespace app\backend\modules\order\controllers;


use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\order\Express;

class BatchSendController extends BaseController
{
    private $originalName;
    private $reader;
    private $success_num = 0;
    private $err_array = [];
    private $error_msg;

    public function __construct()
    {
        // 生成目录
        if (!is_dir(storage_path('app/public/orderexcel'))) {
            mkdir(storage_path('app/public/orderexcel'), 0777);
        }
    }

    public function index()
    {
        $send_data = request()->send;
        if (\Request::isMethod('post')) {
            if ($send_data['express_company_name'] == "顺丰" && $send_data['express_code'] != "shunfeng") {
                return $this->message('上传失败，请重新上传', Url::absoluteWeb('order.batch-send.index'), 'error');
            }

            if (!$send_data['excelfile']) {
                return $this->message('请上传文件', Url::absoluteWeb('order.batch-send.index'), 'error');
            }

            if ($send_data['excelfile']->isValid()) {

                $this->uploadExcel($send_data['excelfile']);
                $this->readExcel();
                $this->handleOrders($this->getRow(), $send_data);

                $msg = $this->success_num . '个订单发货成功。';
                return $this->message($msg . $this->error_msg, Url::absoluteWeb('order.batch-send.index'));
            }
        }

        return view('order.batch_send', [])->render();
    }

    /**
     * @name 保存excel文件
     * @author
     * @param $file
     * @throws ShopException
     */
    private function uploadExcel($file)
    {
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        if (!in_array($ext, ['xls', 'xlsx'])) {
            throw new ShopException('不是xls、xlsx文件格式！');
        }

        $newOriginalName = md5($originalName . str_random(6)) . $ext;
        \Storage::disk('orderexcel')->put($newOriginalName, file_get_contents($realPath));

        $this->originalName = $newOriginalName;
    }

    /**
     * @name 读取文件
     * @author
     */
    private function readExcel()
    {
        $this->reader = \Excel::load(storage_path('app/public/orderexcel') . '/' . $this->originalName);
    }

    /**
     * @name 获取表格内容
     * @author
     * @return array
     */
    private function getRow()
    {
        $values = [];
        $sheet = $this->reader->getActiveSheet();
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
        return $values;
    }

    /**
     * @name 订单发货
     * @author
     * @param $values
     * @param $send_data
     */
    private function handleOrders($values, $send_data)
    {
        foreach ($values as $rownum => $col) {
            $order_sn = trim($col[0]);
            $express_sn = trim($col[1]);
            if (empty($order_sn)) {
                continue;
            }
            if (empty($express_sn)) {
                $this->err_array[] = $order_sn;
                continue;
            }
            $order = Order::select('id', 'order_sn', 'status', 'refund_id')->whereStatus(1)->whereOrderSn($order_sn)->first();
            if (!$order) {
                $this->err_array[] = $order_sn;
                continue;
            }
            $express_model = Express::where('order_id',$order->id)->first();

            !$express_model && $express_model = new Express();

            $express_model->order_id = $order->id;
            $express_model->express_company_name = $send_data['express_company_name'];
            $express_model->express_code = $send_data['express_code'];
            $express_model->express_sn = $express_sn;
            $express_model->save();
            $order->send_time = time();
            $order->status = 2;
            $order->save();
            $this->success_num += 1;
        }
        $this->setErrorMsg();
    }

    /**
     * @name 设置错误信息
     * @author
     */
    private function setErrorMsg()
    {
        if (count($this->err_array) > 0) {
            $num = 1;
            $this->error_msg = '<br>' . count($this->err_array) . '个订单发货失败,失败的订单编号: <br>';
            foreach ($this->err_array as $k => $v )
            {
                $this->error_msg .= $v . ' ';
                if (($num % 2) == 0)
                {
                    $this->error_msg .= '<br>';
                }
                ++$num;
            }
        }
    }

    /**
     * @name 获取示例excel
     * @author
     */
    public function getExample()
    {
        $export_data[0] = ["订单编号", "快递单号"];
        \Excel::create('批量发货数据模板', function ($excel) use ($export_data) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        })->export('xls');
    }
}