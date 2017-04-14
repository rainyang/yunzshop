<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/14
 * Time: 下午2:58
 */

namespace app\frontend\modules\dispatch\controllers;


use app\common\components\ApiController;

class ExpressListController extends ApiController
{
    public function index()
    {
        $expressCompanies =
            '[{"name":"申通","code":"shentong"},{"name":"圆通","code":"yuantong"},{"name":"中通","code":"zhongtong"},{"name":"汇通","code":"huitongkuaidi"},{"name":"韵达","code":"yunda"},{"name":"顺丰","code":"shunfeng"},{"name":"ems","code":"ems"},{"name":"天天","code":"tiantian"},{"name":"宅急送","code":"zhaijisong"},{"name":"邮政","code":"youzhengguonei"},{"name":"德邦","code":"debangwuliu"},{"name":"全峰","code":"quanfengkuaidi"}]';
        $this->successJson('成功', ['express_companies' => $expressCompanies]);
    }
}