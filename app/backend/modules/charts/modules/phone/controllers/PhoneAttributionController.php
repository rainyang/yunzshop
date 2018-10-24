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
        $phone_data = DB::select("select province,count(id) from ims_yz_phone_attribution group BY province");
//        dd($phone_data);
        return view('charts.phone.phone_attribution',[
            'phone_data' => $phone_data
        ]);
    }

}