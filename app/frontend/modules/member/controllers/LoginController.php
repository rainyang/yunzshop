<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\factory\MemberFactory;
use app\frontend\modules\member\services\MemberService;

class LoginController extends BaseController
{
    public function index()
    {
        if (MemberService::isLogged()) {
            return $this->errorJson('用户已登录');
        }

        if (SZ_YI_DEBUG) {
            session()->put('member_id',9);
        }

        $type = \YunShop::request()->type;

        if (!empty($type)) {
                $member = MemberFactory::create($type);

                if ($member !== NULL) {
                    $msg = $member->login();
                    $msg = json_decode($msg);
                    if ($msg->status == 1) {
                        return $this->successJson($msg->result);
                    } else {
                        return $this->errorJson($msg->result);
                    }
                } else {
                    return $this->errorJson('登录异常');
                }
        } else {
            return $this->errorJson('登录失败');
        }
    }
}