<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:55
 */

namespace app\backend\modules\charts\modules\phone\controllers;


use app\backend\modules\charts\modules\phone\models\Member;
use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class PhoneAttributionController extends BaseController
{

    public function index()
    {
        $member = $this->getPhone();
    }

    public function getPhone()
    {
        $uniacid = \YunShop::app()->uniacid;
        $member_phone = DB::select('select uid,mobile,uniacid from ims_mc_members where uniacid ='.$uniacid);

        return $member_phone;
    }
}