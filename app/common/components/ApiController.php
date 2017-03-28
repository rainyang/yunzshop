<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\common\helpers\Url;
use app\frontend\modules\member\services\MemberService;

class ApiController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        if (!MemberService::isLogged()) {
            return $this->errorJson('用户未登录', ['url'=>Url::absoluteApp('member.login')]);
        }
    }
}