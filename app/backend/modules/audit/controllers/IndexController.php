<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/18
 * Time: 下午9:21
 */

namespace app\backend\modules\audit;

use app\common\components\BaseController;
use app\common\models\flow\AuditFlow;

class IndexController extends BaseController
{
    public function index()
    {
        $auditFlow = AuditFlow::first();
        /**
         * @var  AuditFlow $auditFlow
         */
        $a = $auditFlow->process;
        dd($a);
        exit;

    }
}