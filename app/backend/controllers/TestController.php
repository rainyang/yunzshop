<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;


use app\backend\modules\charts\modules\member\services\LowerCountService;
use app\backend\modules\charts\modules\member\services\LowerOrderService;
use app\backend\models\Withdraw;
use app\backend\modules\charts\models\OrderIncomeCount;
use app\backend\modules\charts\modules\order\services\OrderStatisticsService;
use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use app\common\components\BaseController;
use app\common\events\member\MemberCreateRelationEvent;
use app\common\events\member\MemberRelationEvent;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Income;
use app\common\models\Member;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use app\common\models\Order;
use app\common\models\OrderGoods;
use app\common\models\OrderPay;
use app\common\models\Flow;
use app\common\models\Setting;
use app\common\services\member\MemberRelation;
use app\common\repositories\ExpressCompany;
use app\common\services\MessageService;
use app\frontend\modules\member\models\SubMemberModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\Listener\OrderCreatedListener;
use Yunshop\Mryt\models\MrytLevelModel;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;


class TestController extends BaseController
{
    public $transactionActions = ['*'];

    public function t()
    {


    }

    public $orderId;

    /**
     * @return bool
     */
    public function index()
    {
        $a = Carbon::createFromTimestamp(1503488629)->diffInDays(Carbon::createFromTimestamp(1504069595), true);
        dd($a);
        $member_relation = new MemberRelation();

        $relation = $member_relation->hasRelationOfParent(66, 5, 1);

        dd($relation);

        $text = '{"commission":{"title":"u5206u9500","data":{"0":{"title":"u5206u9500u4f63u91d1","value":"0.01u5143"},"2":{"title":"u4f63u91d1u6bd4u4f8b","value":"0.15%"},"3":{"title":"u7ed3u7b97u5929u6570","value":"0u5929"},"4":{"title":"u4f63u91d1u65b9u5f0f","value":"+u5546u54c1u72ecu7acbu4f63u91d1"},"5":{"title":"u5206u4f63u65f6u95f4","value":"2018-10-24 09:15:31"},"6":{"title":"u7ed3u7b97u65f6u95f4","value":"2018-10-24 09:20:04"}}},"order":{"title":"u8ba2u5355","data":[{"title":"u8ba2u5355u53f7","value":"SN201810 24091508a8"},{"title":"u72b6u6001","value":"u4ea4u6613u5b8cu6210"}]},"goods":{"title":"u5546u54c1","data":[[{"title":"u540du79f0","value":"u8700u9999u98ceu7fd4u8c46u82b1u6ce1u998du5e97"},{"title":"u91d1u989d","value":"4.00u5143"}]]}}';

        $pattern1 = '/\\\u[\d|\w]{4}/';
        preg_match($pattern1, $text, $exists);

        if (empty($exists)) {
            $pattern2 = '/(u[\d|\w]{4})/';

    }

    public function op_database()
    {
        $sub_data = array(
            'member_id' => 999,
            'uniacid' => 5,
            'group_id' => 0,
            'level_id' => 0,
        );

        SubMemberModel::insertData($sub_data);

        if (SubMemberModel::insertData($sub_data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }

    }

    public function notice()
    {
        $teamDividendNotice = \Setting::get('plugin.team_dividend');

        $member = Member::getMemberById(\YunShop::app()->getMemberId());

        if ($teamDividendNotice['template_id']) {
            $message = $teamDividendNotice['team_agent'];
            $message = str_replace('[昵称]', $member->nickname, $message);
            $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
            $message = str_replace('[团队等级]', '一级', $message);

            $msg = [
                "first" => '您好',
                "keyword1" => "成为团队代理通知",
                "keyword2" => $message,
                "remark" => "",
            ];
            echo '<pre>';
            print_r($msg);
            MessageService::notice($teamDividendNotice['template_id'], $msg, 'oNnNJwqQwIWjAoYiYfdnfiPuFV9Y');

        }
        return;
    }

    public function fixImage()
    {
        $goods = DB::table('yz_goods')->get();
        $goods_success = 0;
        $goods_error = 0;
        foreach ($goods as $item) {

            if ($item['thumb'] && !preg_match('/^images/', $item['thumb'])) {

                $src = $item['thumb'];
                if (strexists($src, '/addons/') || strexists($src, 'yun_shop/') || strexists($src, '/static/')) {
                    continue;
                }

                if (preg_match('/\/images/', $item['thumb'])) {
                    $thumb = substr($item['thumb'], strpos($item['thumb'], 'images'));
                    $bool = DB::table('yz_goods')->where('id', $item['id'])->update(['thumb' => $thumb]);

                    if ($bool) {
                        $goods_success++;
                    } else {
                        $goods_error++;
                    }
                }
            }
        }


        $category = DB::table('yz_category')->get();
        $category_success = 0;
        $category_error = 0;
        foreach ($category as $item) {
            $src = $item['thumb'];
            if (strexists($src, 'addons/') || strexists($src, 'yun_shop/') || strexists($src, 'static/')) {
                continue;
            }

            if ($item['thumb'] && !preg_match('/^images/', $item['thumb'])) {
                if (preg_match('/\/images/', $item['thumb'])) {
                    $thumb = substr($item['thumb'], strpos($item['thumb'], 'images'));
                    $bool = DB::table('yz_category')->where('id', $item['id'])->update(['thumb' => $thumb]);
                    if ($bool) {
                        $category_success++;
                    } else {
                        $category_error++;
                    }
                }
            }
        }


        echo '商品图片修复成功：' . $goods_success . '个,失败：' . $goods_error . '个';
        echo '<br />';
        echo '分类图片修复成功：' . $category_success . '个，失败：' . $category_error . '个';

    }


    public function getPhone()
    {
        (new PhoneAttributionService())->phoneStatistics();
    }

    public function tt()
    {
        $member_relation = new MemberRelation();

        $member_relation->createParentOfMember();
    }

    public function fixIncome()
    {
        $count = 0;
        $income = Income::whereBetween('created_at', [1539792000, 1540915200])->get();
        foreach ($income as $value) {
            $pattern1 = '/\\\u[\d|\w]{4}/';
            preg_match($pattern1, $value->detail, $exists);
            if (empty($exists)) {
                $pattern2 = '/(u[\d|\w]{4})/';
                $value->detail = preg_replace($pattern2, '\\\$1', $value->detail);
                $value->save();
                $count++;
            }
        }
        echo "修复了{$count}条";
    }

    public function pp()
    {

        //$this->synRun(5, '');exit;

        $member_relation = new MemberRelation();

        $member_relation->createChildOfMember();
    }

    public function synRun($uniacid, $memberInfo)
    {
        $memberModel = new \app\backend\modules\member\models\Member();
        $childMemberModel = new ChildrenOfMember();
        $parentMemberModle = new ParentOfMember();

        $memberInfo = $memberModel->getTreeAllNodes($uniacid);

        if ($memberInfo->isEmpty()) {
            \Log::debug('----is empty-----');
            return;
        }

        //$memberInfo = $memberInfo;

        $memberModel->_allNodes = collect([]);
        foreach ($memberInfo as $item) {
            $memberModel->_allNodes->put($item->member_id, $item);
        }

        //dd($memberModel->_allNodes);
        /* \Log::debug('--------queue member_model -----', get_class($this->memberModel));
         \Log::debug('--------queue childMemberModel -----', get_class($this->childMemberModel));*/
        \Log::debug('--------queue synRun -----');

        foreach ($memberInfo as $key => $val) {
            $attr = [];
            echo '-------' . $key . '--------' . $val->member_id . '<BR>';
            \Log::debug('--------foreach start------', $val->member_id);
            //$data = $memberModel->getNodeParents($uniacid, $val->member_id);
            $data = $memberModel->getDescendants($uniacid, $val->member_id);

            \Log::debug('--------foreach data------', $data->count());

            if (!$data->isEmpty()) {
                \Log::debug('--------insert init------');
                $data = $data->toArray();

                foreach ($data as $k => $v) {
                    $attr[] = [
                        'uniacid' => $uniacid,
                        'child_id' => $k,
                        'level' => $v['depth'] + 1,
                        'member_id' => $val->member_id,
                        'created_at' => time()
                    ];
                }

                $childMemberModel->createData($attr);
                /*
                 foreach ($data as $k => $v) {
                     $attr[] = [
                         'uniacid'   => $uniacid,
                         'parent_id'  => $k,
                         'level'     => $v['depth'] + 1,
                         'member_id' => $val->member_id,
                         'created_at' => time()
                     ];
                 }

                 $parentMemberModle->createData($attr);*/
            }


        }

        echo 'end';

    }

}