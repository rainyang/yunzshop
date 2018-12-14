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
//use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Yunshop\Commission\models\Agents;
//use Yunshop\Kingtimes\common\models\Distributor;
//use Yunshop\Kingtimes\common\models\Provider;
//use Yunshop\Poster\models\Poster;
//use Yunshop\Poster\services\CreatePosterService;
//use Yunshop\TeamDividend\models\YzMemberModel;
//use Yunshop\AlipayOnekeyLogin\services\SynchronousUserInfo;
use app\common\services\alipay\OnekeyLogin;
use app\common\helpers\Client;
//use app\common\services\plugin\huanxun\HuanxunSet;

class MemberRelationController extends ApiController
{
    /**
     *  推广申请页面数据
     */
    public function index() {
        //判断用户是否能申请

        $data = MemberRelation::uniacid()->where(['status'=>1])->get()->toArray();

        $data[0]['become_goods'] = unserialize($data[0]['become_goods']);
        $data[0]['become_term'] = unserialize($data[0]['become_term']);

        $goodskeys = range(0, count($data[0]['become_goods'])-1);
        $data[0]['become_goods'] = array_combine($goodskeys, $data[0]['become_goods']);

        $termskeys = range(0, count($data[0]['become_term'])-1);
        $data[0]['become_term'] = array_combine($termskeys, $data[0]['become_term']);

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