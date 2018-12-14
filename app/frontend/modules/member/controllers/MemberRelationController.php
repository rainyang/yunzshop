<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/12
 * Time: 16:12
 */
namespace app\frontend\modules\member\controllers;

use app\backend\modules\charts\modules\phone\models\PhoneAttribution;
use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use app\backend\modules\member\models\MemberRelation;
use app\backend\modules\order\models\Order;
use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Cache;
use app\common\helpers\ImageHelper;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Area;
use app\common\models\Goods;
use app\common\models\Orders;
use app\common\models\McMappingFans;
use app\common\models\MemberShopInfo;
use app\common\services\popularize\PortType;
use app\common\services\Session;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\models\OrderListModel;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Yunshop\Commission\models\Agents;
use Yunshop\Kingtimes\common\models\Distributor;
use Yunshop\Kingtimes\common\models\Provider;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\services\CreatePosterService;
use Yunshop\TeamDividend\models\YzMemberModel;
use Yunshop\AlipayOnekeyLogin\services\SynchronousUserInfo;
use app\common\services\alipay\OnekeyLogin;
use app\common\helpers\Client;
use app\common\services\plugin\huanxun\HuanxunSet;

class MemberRelationController extends ApiController
{
    /**
     *  推广申请页面数据
     */
    public function index() {
        //判断用户是否能申请

        $data = MemberRelation::uniacid()->where(['status'=>1])->get();

        $become_goods = unserialize($data[0]['become_goods']);
        $become_term = unserialize($data[0]['become_term']);

        $goodskeys = range(0, count($become_goods)-1);
        $data[0]['become_goods'] = array_combine($goodskeys, $become_goods);

        $termskeys = range(0, count($become_term)-1);
        $become_term = array_combine($termskeys, $become_term);

        $member_uid = \YunShop::app()->getMemberId();

        $getCostTotalNum = Order::getCostTotalNum($member_uid);
        $getCostTotalPrice = Order::getCostTotalPrice($member_uid);

        $data[0]['getCostTotalNum'] = $getCostTotalNum;
        $data[0]['$getCostTotalPrice'] = $getCostTotalPrice;

        $terminfo = [];

        foreach ($become_term as $v) {
            if ($v == 2) {
                $terminfo['become_ordercount'] = $data[0]['become_ordercount'];
            }
            if ($v == 3) {
                $terminfo['become_moneycount'] = $data[0]['become_moneycount'];
            }
            if ($v == 4) {
                $terminfo['goodsinfo'] = $data[0]['become_goods'];
            }
            if ($v == 5) {
                $terminfo['become_selfmoney'] = $data[0]['become_selfmoney'];
            }
        }
        $data[0]['become_term'] = $terminfo;

        if ($data[0]['become'] == 2) {
            //或
            $data[0]['tip'] = '满足以下任意条件都可以升级';
        } elseif ($data[0]['become'] == 3) {
            //与
            $data[0]['tip'] = '满足以下所有条件才可以升级';
        }
        return $this->successJson('ok', $data[0]);
    }
}