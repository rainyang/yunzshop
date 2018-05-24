<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午6:08
 */

namespace app\common\components;

class AdminApiController extends BaseController
{
    public function __construct()
    {
        dd(app());
        parent::__construct();
    }
}
