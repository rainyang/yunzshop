<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\helpers\Client;
use app\frontend\modules\member\services\factory\MemberFactory;

class LoginController extends ApiController
{
    protected $publicAction = ['index'];

    public function index()
    {
        $type = \YunShop::request()->type ;

        if (empty($type)) {
            $type = Client::getType();
        }

        if (!empty($type)) {
                $member = MemberFactory::create($type);

                if ($member !== NULL) {
                    $msg = $member->login();
//                    $msg = json_decode($msg);
//
//                    if (!empty($msg)) {
//                        if ($msg->status == 1) {
//                            return $this->successJson('', $msg->result);
//                        } else {
//                            return $this->errorJson('', $msg->result);
//                        }
//                    } else {
//                        echo $this->errorJson('', 500);
//                    }
                } else {
                    return $this->errorJson('登录异常', ['status'=>-1]);
                }
        } else {
            return $this->errorJson('登录失败', ['status'=>0]);
        }
    }
}