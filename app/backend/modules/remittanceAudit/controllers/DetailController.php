<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/22
 * Time: 上午11:22
 */

namespace app\backend\modules\remittanceAudit\controllers;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;

class DetailController extends BaseController
{
    protected $process;

    /**
     * DetailController constructor.
     * @throws AppException
     */
    public function __construct()
    {
        parent::__construct();
        $processId = request()->input('id');
        $this->process = RemittanceAuditProcess::with(['remittanceRecord'=> function ($query) {
            $query->with('orderPay');
        },'member'])->find($processId);
        if(!isset($this->process)){
            if(!isset($this->process)){
                throw new AppException("我找到id为{$processId}的审核进程记录");
            }
        }
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {

        return view('remittanceAudit.detail', [
            'remittanceAudit' => json_encode($this->process)
        ])->render();
    }
}