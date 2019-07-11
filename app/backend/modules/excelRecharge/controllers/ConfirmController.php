<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-19
 * Time: 16:56
 */

namespace app\backend\modules\excelRecharge\controllers;


use app\backend\models\excelRecharge\RecordsModel;
use app\common\helpers\Url;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\PointService;
use Yunshop\Love\Common\Services\LoveChangeService;

class ConfirmController extends PageController
{
    private $fileContent;

    /**
     * @var int
     */
    private $handleNum = 0;

    /**
     * @var int
     */
    private $errorNum = 0;

    /**
     * @var string
     */
    private $handAmount = '0';


    /**
     * @var string
     */
    private $successAmount = '0';


    //批量充值提交
    public function index()
    {
        if ($this->rechargeType() && $this->rechargeTypeValidator()) {
            return $this->confirm();
        }
        return $this->typeError();
    }

    /**
     * 确认充值
     *
     * @return mixed|void
     */
    private function confirm()
    {
        $excelFile = $this->excelFile();

        if ($excelFile
            && $excelFile->isValid()
            && in_array($excelFile->getClientOriginalExtension(), ['xls', 'xlsx'])
        ) {
            return $this->rechargeStart();
        }
        return $this->excelError();
    }

    private function rechargeStart()
    {
        $recordsModel = new RecordsModel();


        $handleRechargeData = $this->handleRecharge($recordsModel->id);


        //todo todo todo
        //添加充值记录

        //充值、插入详细记录

        //更新充值记录

        return $this->message($this->messageContent(), Url::absoluteWeb('excelRecharge.records.index'));
    }

    /**
     * @return string
     */
    private function messageContent()
    {
        return "执行数量{$this->handleNum}，成功数量{$this->successNum}，失败数量{$this->errorNum}";
    }

    private function handleRecharge(&$recordsId)
    {
        $this->uploadExcel();

        $data = [];
        $values = $this->getRow();
        foreach ($values as $key => $value) {

            $amount = trim($value[1]);
            $memberId = trim($value[0]);

            $data[] = $this->_handleRecharge($recordsId, $memberId, $amount);
        }
        return $data;
    }

    private function _handleRecharge($recordsId, $memberId, $amount)
    {
        $this->handleNum += 1;
        $this->handAmount += $amount;


        try {
            $rechargeFunc = $this->batchRechargeFunc();

            $result = $this->$rechargeFunc($memberId, $amount);;

        } catch (\Exception $exception) {

            $result = $exception->getMessage();
        }

        if ($result === true) {
            $status = 1;
            $remark = '';
            $this->successAmount += $amount;
        } else {
            $this->errorNum += 1;

            $status = 0;
            $remark = "充值失败" . $result;
        }

        return [
            'uniacid'     => \YunShop::app()->uniacid,
            'amount'      => $amount,
            'member_id'   => $memberId,
            'recharge_id' => $recordsId,
            'remark'      => $remark,
            'status'      => $status

        ];
    }

    /**
     * 充值类型错误
     *
     * @return mixed
     */
    private function typeError()
    {
        return $this->errorMessage("错误的批量充值类型");
    }

    /**
     * Excel 错误
     *
     * @return mixed
     */
    private function excelError()
    {
        return $this->errorMessage("错误的Excel文件");
    }

    /**
     * 错误
     *
     * @param $message
     * @return mixed
     */
    private function errorMessage($message)
    {

        $this->error($message);

        return parent::index();
    }

    /**
     * 批量充值类型方法
     *
     * @return string
     */
    private function batchRechargeFunc()
    {
        $batchType = $this->rechargeType();

        $func = 'batchRecharge' . ucfirst(strtolower($batchType));

        return $func;
    }

    /**
     * 批量充值类型
     *
     * @return string
     */
    private function rechargeType()
    {
        return request()->batch_type;
    }

    /**
     * 批量充值类型验证
     *
     * @return bool
     */
    private function rechargeTypeValidator()
    {
        if (is_callable(static::class, $this->batchRechargeFunc())) {
            return true;
        }
        return false;
    }

    /**
     * 批量充值Excel文件
     *
     * @return array|\Illuminate\Http\UploadedFile|null
     */
    private function excelFile()
    {
        return request()->file('batch_recharge');
    }

    /**
     * 上传批量充值Excel文件
     */
    private function uploadExcel()
    {
        $excelFile = $this->excelFile();

        $realPath = $excelFile->getRealPath();
        $extension = $excelFile->getClientOriginalExtension();
        $originalName = $excelFile->getClientOriginalName();

        $newOriginalName = md5($originalName . str_random(6)) . "." . $extension;

        \Storage::disk('recharge')->put($newOriginalName, file_get_contents($realPath));

        $this->fileContent = \Excel::load(storage_path($this->path) . '/' . $newOriginalName);
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     */
    private function getRow()
    {
        $sheet = $this->fileContent->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $row = 2;

        $values = [];
        while ($row <= $highestRow) {
            $rowValue = array();
            $col = 0;
            while ($col < $highestColumnCount) {
                $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                ++$col;
            }
            $values[] = $rowValue;
            ++$row;
        }
        return $values;
    }


    private function batchRechargeBalance($memberId, $rechargeValue)
    {
        return (new BalanceChange())->excelRecharge([
            'member_id'    => $memberId,
            'change_value' => $rechargeValue,
            'remark'       => 'Excel批量充值' . $rechargeValue . "元",
            'relation'     => '',
            'operator'     => ConstService::OPERATOR_SHOP,
            'operator_id'  => \YunShop::app()->uid
        ]);

    }

    private function batchRechargePoint($memberId, $rechargeValue)
    {
        return (new PointService([
            'point_mode'        => PointService::POINT_MODE_EXCEL_RECHARGE,
            'member_id'         => $memberId,
            'point'             => $rechargeValue,
            'remark'            => 'Excel批量充值' . $rechargeValue,
            'point_income_type' => $rechargeValue < 0 ? PointService::POINT_INCOME_LOSE : PointService::POINT_INCOME_GET
        ]))->changePoint();
    }



    private function batchRechargeLove($memberId, $rechargeValue, $love_type)
    {
        return (new LoveChangeService($love_type))->recharge([
            'member_id'    => $memberId,
            'change_value' => $rechargeValue,
            'operator'     => ConstService::OPERATOR_MEMBER,
            'operator_id'  => 0,
            'remark'       => 'Excel批量充值' . $rechargeValue,
        ]);
    }

}
