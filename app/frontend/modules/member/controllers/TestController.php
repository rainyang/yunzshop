<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/9
 * Time: 上午11:40
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;

class TestController extends BaseController
{
   public function index()
   {
       $member_info = MemberModel::getUserInfos(11)->first();

       if (!empty($member_info)) {
           $member_info = $member_info->toArray();

           return $this->successJson($member_info);
       } else {
           return $this->errorJson('用户不存在');
       }
   }
}