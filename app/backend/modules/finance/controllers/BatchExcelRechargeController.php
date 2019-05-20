<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/8
 * Time: 10:52 AM
 */

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\PointService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class BatchExcelRechargeController extends BaseController
{
    private $path = 'app/public/recharge';


    private $fileName;


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
     * @var int
     */
    private $successNum = 0;


    public function __construct()
    {
        parent::__construct();

        $this->makeFilePath();
    }

    /**
     * 批量充值页面
     */
    public function index()
    {
        $love_open =0;
        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_open = 1;
            $love_name = SetService::getLoveName();
        }
        return view('finance.batchExcelRecharge', [
            'love_open' => $love_open,
            'love_name' => $love_name,
        ]);
    }

    /**
     * 批量充值提交
     */
    public function confirm()
    {
        if ($this->batchType() && $this->batchTypeValidator()) {
            return $this->_confirm();
        }
        return $this->batchTypeError();
    }

    /**
     * 批量充值EXCEL模版
     */
    public function example()
    {
        $exportData[0] = ["会员ID", "充值数量"];

        \Excel::create('批量充值模板', function ($excel) use ($exportData) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城');
            $excel->setLastModifiedBy("芸众商城");
            $excel->setSubject("Office 2005 XLSX Test Document");
            $excel->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.");
            $excel->setKeywords("office 2005 openxml php");
            $excel->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($exportData) {
                $sheet->rows($exportData);
            });
        })->export('xls');
    }

    /**
     * 批量充值提交（2）
     */
    private function _confirm()
    {
        $excelFile = $this->excelFile();

        if ($excelFile && $excelFile->isValid()) {
            return $this->batchRecharge();
        }
        return $this->batchExcelError();
    }


    /**
     * 创建目录
     */
    private function makeFilePath()
    {
        if (!is_dir(storage_path($this->path))) {
            mkdir(storage_path($this->path), 0777);
        }
    }

    private function batchRecharge()
    {
        $this->uploadExcel();
        $this->readExcel();

        $func = $this->batchRechargeFunc();
        $this->$func();

        $msg = "执行数量{$this->handleNum}，成功数量{$this->successNum}，失败数量{$this->errorNum}";
        return $this->message($msg, Url::absoluteWeb('finance.batch-excel-recharge.index'));
    }

    private function batchRechargeBalance()
    {
        $values = $this->getRow();

        foreach ($values as $value) {
            $this->handleNum += 1;

            $memberId = trim($value[0]);
            $rechargeValue = trim($value[1]);

            if (!$memberId || !$rechargeValue || $rechargeValue < 0) {
                continue;
            }
            $this->balanceRecharge($memberId, $rechargeValue);
        }
    }

    private function balanceRecharge($memberId, $rechargeValue)
    {
        try {
            $result = (new BalanceChange())->excelRecharge([
                'member_id'    => $memberId,
                'change_value' => $rechargeValue,
                'remark'       => 'Excel批量充值' . $rechargeValue . "元",
                'relation'     => '',
                'operator'     => ConstService::OPERATOR_SHOP,
                'operator_id'  => \YunShop::app()->uid
            ]);
            if ($result === true) {
                $this->successNum += 1;
            } else {
                $this->errorNum += 1;
            }
        } catch (\Exception $exception) {

            $this->errorNum += 1;
        }
    }

    private function batchRechargePoint()
    {
        $values = $this->getRow();

        foreach ($values as $key => $value) {

            $this->handleNum += 1;
            $memberId = trim($value[0]);
            $rechargeValue = trim($value[1]);

            if (!$memberId || !$rechargeValue && $rechargeValue < 0) {
                continue;
            }

            $this->pointRecharge($memberId, $rechargeValue);
        }
    }

    private function pointRecharge($memberId, $rechargeValue)
    {
        try {
            $result = (new PointService([
                'point_mode'        => PointService::POINT_MODE_EXCEL_RECHARGE,
                'member_id'         => $memberId,
                'point'             => $rechargeValue,
                'remark'            => 'Excel批量充值' . $rechargeValue,
                'point_income_type' => $rechargeValue < 0 ? PointService::POINT_INCOME_LOSE : PointService::POINT_INCOME_GET
            ]))->changePoint();


            if ($result) {
                $this->successNum += 1;
            } else {
                $this->errorNum += 1;
            }
        } catch (\Exception $exception) {

            $this->errorNum += 1;
        }
    }

    private function batchRechargeLove()
    {
        $love_type = request()->love_type;
        $values = $this->getRow();

        foreach ($values as $key => $value) {

            $this->handleNum += 1;
            $memberId = trim($value[0]);
            $rechargeValue = trim($value[1]);

            if (!$memberId || !$rechargeValue && $rechargeValue < 0) {
                continue;
            }

            $this->LoveRecharge($memberId, $rechargeValue, $love_type);
        }
    }

    private function LoveRecharge($memberId, $rechargeValue, $love_type)
    {
        try {
            $result = (new LoveChangeService($love_type))->recharge([
                'member_id'     => $memberId,
                'change_value'  => $rechargeValue,
                'operator'      => ConstService::OPERATOR_MEMBER,
                'operator_id'   => 0,
                'remark'        => 'Excel批量充值'.$rechargeValue,
            ]);

            if ($result === true) {
                $this->successNum += 1;
            } else {
                $this->errorNum += 1;
            }
        } catch (\Exception $exception) {

            $this->errorNum += 1;
        }
    }

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

    /**
     * 上传批量充值Excel文件
     *
     * @throws ShopException
     */
    private function uploadExcel()
    {
        $excelFile = $this->excelFile();

        $originalName = $excelFile->getClientOriginalName();
        $extension = $excelFile->getClientOriginalExtension();
        $realPath = $excelFile->getRealPath();
        if (!in_array($extension, ['xls', 'xlsx'])) {
            throw new ShopException('不是xls、xlsx文件格式！');
        }

        $newOriginalName = md5($originalName . str_random(6)) . "." . $extension;
        \Storage::disk('recharge')->put($newOriginalName, file_get_contents($realPath));

        $this->fileName = $newOriginalName;
    }

    /**
     * 读取上传的批量充值Excel文件
     */
    private function readExcel()
    {
        $this->fileContent = \Excel::load(storage_path($this->path) . '/' . $this->fileName);
    }

    /**
     * 批量充值类型验证
     *
     * @return bool
     */
    private function batchTypeValidator()
    {
        if (is_callable(static::class, $this->batchRechargeFunc())) {
            return true;
        }
        return false;
    }

    /**
     * 批量充值类型方法
     *
     * @return string
     */
    private function batchRechargeFunc()
    {
        $batchType = $this->batchType();

        $func = 'batchRecharge' . ucfirst(strtolower($batchType));

        return $func;
    }

    /**
     * 批量充值Excel文件错误
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function batchExcelError()
    {
        $this->error('错误的Excel文件');

        return $this->index();
    }

    /**
     * 批量充值类型错误
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function batchTypeError()
    {
        $this->error('错误的批量充值类型');

        return $this->index();
    }

    /**
     * 批量充值类型
     *
     * @return string
     */
    private function batchType()
    {
        return request()->batch_type;
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
}
