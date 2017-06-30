<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午11:32
 */

namespace app\backend\widgets\finance;

use app\common\components\Widget;
use app\common\facades\Setting;

class WithdrawNoticeWidget extends Widget
{

    public function run()
    {
        $set = Setting::get('withdraw.notice');
        return view('finance.withdraw.withdraw-notice', [
            'set' => $set,
        ])->render();
    }
}

