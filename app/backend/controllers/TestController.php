<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\events\member\MemberCreateRelationEvent;
use app\common\events\member\MemberRelationEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Member;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use app\common\models\Order;

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

class TestController extends BaseController
{
    public function index()
    {

        $member_relation = new MemberRelation();

        $relation = $member_relation->hasRelationOfParent(66, 5, 1);

        dd($relation);

        $text = '{"commission":{"title":"u5206u9500","data":{"0":{"title":"u5206u9500u4f63u91d1","value":"0.01u5143"},"2":{"title":"u4f63u91d1u6bd4u4f8b","value":"0.15%"},"3":{"title":"u7ed3u7b97u5929u6570","value":"0u5929"},"4":{"title":"u4f63u91d1u65b9u5f0f","value":"+u5546u54c1u72ecu7acbu4f63u91d1"},"5":{"title":"u5206u4f63u65f6u95f4","value":"2018-10-24 09:15:31"},"6":{"title":"u7ed3u7b97u65f6u95f4","value":"2018-10-24 09:20:04"}}},"order":{"title":"u8ba2u5355","data":[{"title":"u8ba2u5355u53f7","value":"SN201810 24091508a8"},{"title":"u72b6u6001","value":"u4ea4u6613u5b8cu6210"}]},"goods":{"title":"u5546u54c1","data":[[{"title":"u540du79f0","value":"u8700u9999u98ceu7fd4u8c46u82b1u6ce1u998du5e97"},{"title":"u91d1u989d","value":"4.00u5143"}]]}}';

        $pattern1 = '/\\\u[\d|\w]{4}/';
        preg_match($pattern1, $text, $exists);

        if (empty($exists)) {
            $pattern2 = '/(u[\d|\w]{4})/';

            $json = preg_replace($pattern2, '\\\$1', $text);
        }

        echo $json;
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
                            'uniacid'   => $uniacid,
                            'child_id'  => $k,
                            'level'     => $v['depth'] + 1,
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
//        dd($level_1_member);
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


    }

    public function qe()
    {
        $uniacid = \YunShop::app()->uniacid;
        $member_1 = DB::select('select uniacid,child_id,level from ims_yz_member_children where level =1'.' and uniacid ='.$uniacid .' order by child_id');

        foreach ($member_1 as $k => $item) {
            $order_1_all[] = DB::select('select uid,sum(price) as money,count(id) as total from ims_yz_order where uid='.$item['child_id']);
        }
//        dd($order_1_all);
        $member_2 = DB::select('select uniacid,child_id,level from ims_yz_member_children where level =2'.' and uniacid ='.$uniacid .' order by child_id');

        foreach ($member_2 as $k => $item) {
            $order_2_all[] = DB::select('select uid,sum(price) as money,count(id) as total from ims_yz_order where uid='.$item['child_id']);
        }
        dd($order_2_all);
    }


    public function qw()
    {
        //团队所有会员
        $uniacid = \YunShop::app()->uniacid;
        $team_member = DB::select('select child_id from ims_yz_member_children where uniacid=' . $uniacid . ' and member_id=1');

        foreach ($team_member as $item) {
            $order_total[] = DB::select("select count(id) as total from ims_yz_order where status in (1,2,3) and uid=" . $item['child_id']);
        }

        //团队订单总数
        $team__total = 0;
        foreach ($order_total as $k => $item) {
            $team__total += $item[0]['total'];
        }

        dd($team__total);
    }

    public function mr()
    {
       /* $a = [1,2,3,4,5];


        foreach ($a as $val) {
            $b = array_shift($a);
        }


        dd($b, $a);

        exit;*/

        $uid = 163764;
        $o_parent_id = 163762;
        $n_parent_id = 163768;

        $member_relation = new MemberRelation();

        $member_relation->build($uid, $n_parent_id);

//        $member = Member::getMemberByUid($uid)->first();
//
//        event(new MemberRelationEvent($member));
 //       event(new MemberCreateRelationEvent($uid, $n_parent_id));exit;
//        (new MemberRelation())->changeMemberOfRelation($uid, $o_parent_id, $n_parent_id);
        //(new MemberRelation())->parent->addNewParentData($uid, $n_parent_id);

    }
}