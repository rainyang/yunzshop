<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:55
 */

namespace app\backend\modules\charts\modules\phone\controllers;


use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class PhoneAttributionController extends BaseController
{

    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;
        $phone_data = DB::select('select province,count(id) as num from ims_yz_phone_attribution where uniacid='.$uniacid.' group BY province');

        return view('charts.phone.phone_attribution',[
            'phone_data' => $phone_data,
            'phone_map_data' => json_encode($phone_data,256),
        ]);
    }

}