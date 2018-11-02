<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;

use app\backend\models\Withdraw;
use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\components\BaseController;
use app\common\events\order\AfterOrderCreatedEvent;
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
use Yunshop\Kingtimes\common\models\CompeteOrderDistributor;
use Yunshop\Kingtimes\common\models\OrderDistributor;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class TestController extends BaseController
{
    public $orderId;
    /**
     * @return bool
     */
    public function index()
    {
        $a = Artisan::call('queue:retry');
        dd($a);
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

    public function tt()
    {

       $this->synRun(5, '');exit;

        $member_relation = new MemberRelation();

        $member_relation->createParentOfMember();
    }

    public function fixIncome()
    {
        $count = 0;
        $income = Income::whereBetween('created_at', [1539792000,1540915200])->get();
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
                $data = $memberModel->getNodeParents($uniacid, $val->member_id);
                //$data = $memberModel->getDescendants($uniacid, $val->member_id);

                \Log::debug('--------foreach data------', $data->count());

                if (!$data->isEmpty()) {
                    \Log::debug('--------insert init------');
                    $data = $data->toArray();

                    /*foreach ($data as $k => $v) {
                        $attr[] = [
                            'uniacid'   => $uniacid,
                            'child_id'  => $k,
                            'level'     => $v['depth'] + 1,
                            'member_id' => $val->member_id,
                            'created_at' => time()
                        ];
                    }

                    $childMemberModel->createData($attr);*/
                    foreach ($data as $k => $v) {
                        $attr[] = [
                            'uniacid'   => $uniacid,
                            'parent_id'  => $k,
                            'level'     => $v['depth'] + 1,
                            'member_id' => $val->member_id,
                            'created_at' => time()
                        ];
                    }

                    $parentMemberModle->createData($attr);
                }


        }

        echo 'end';

    }

    public function wf()
    {
        $uniacid = \YunShop::app()->uniacid;
        //团队总人数
        $team_member = DB::select('select child_id from ims_yz_member_children where uniacid='.$uniacid.' and member_id=1');
        $team_member_count = DB::select('select count(child_id) as c from ims_yz_member_children where uniacid='.$uniacid.' and member_id=1');
        $team_all = $team_member_count[0]['c'];

        foreach ($team_member as $item) {
            $order_money[] = DB::select("select sum(price) as price from ims_yz_order where status in (1,2,3) and uid=".$item['child_id']);
        }
        //团队订单总金额
        $team_money_total = 0;
        foreach ($order_money as $k => $item) {
            $team_money_total+= $item[0]['price'];
        }

        return $this->successJson('ok', [
            'team_all' => $team_all,
            'team_money_total' => $team_money_total
        ]);
    }

    public function ww()
    {
        $uniacid = \YunShop::app()->uniacid;
        $level_1_member = DB::select('select member_id,level,count(1) as total from ims_yz_member_children where uniacid='.$uniacid.' and level in (1,2,3) group by member_id,level');
        $level_1_member = collect($level_1_member);
        $result = [];
        dd($level_1_member);
        foreach ($level_1_member as $val) {
            if (!isset($result[$val['member_id']])) {
                 $result[$val['member_id']] = [
                     'member_id' => $val['member_id'],
                     'first_total' => $val['total'],
                     'second_total' => 0,
                     'third_total' => 0,
                     'team_total'  => $val['total']
                 ];
            } else {
                switch ($val['level']) {
                    case 2:
                        $result[$val['member_id']]['second_total'] = $val['total'];
                        break;
                    case 3:
                        $result[$val['member_id']]['third_total'] = $val['total'];
                        break;
                }

                $result[$val['member_id']]['team_total'] += $val['total'];
            }
        }

//dd($result);
        $level_2_member = DB::select('select member_id,count(1) as total from ims_yz_member_children where uniacid='.$uniacid.' and level=2 group by member_id,level');

        $level_3_member = DB::select('select member_id,count(1) as total from ims_yz_member_children where uniacid='.$uniacid.' and level=3 group by member_id,level');

    }
}