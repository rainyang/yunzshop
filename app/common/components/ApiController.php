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
    protected $publicAction = [];

    public function __construct()
    {
        parent::__construct();

      //  if (!MemberService::isLogged() && !in_array($this->action,$this->publicAction)) {
      //      return $this->errorJson('用户未登录', ['url'=>Url::absoluteApp('member.login.index')]);
      //  }
    }
}