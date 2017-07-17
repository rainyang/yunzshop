<?php
namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\exceptions\AppException;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\Order;
use app\common\services\MessageService;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\services\MemberService;

use app\frontend\modules\order\services\message\Message;
use app\frontend\modules\order\services\OrderManager;
use app\frontend\modules\order\services\OrderService;

use Illuminate\Support\Facades\App;
use Yunshop\Gold\common\services\Notice;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public function index()
    {
        echo 2;
        \Log::info(1);
        exit;
    }

}