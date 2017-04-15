<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 上午10:11
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\components\BaseController;
use app\backend\modules\member\models\MemberRelation as Relation;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;

class MemberRelationController extends BaseController
{
    public $pageSize = 20;

    /**
     * 列表
     *
     * @return string
     */
    public function index()
    {

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation = $relation->toArray();
        }

        if (!empty($relation['become_goods_id'])) {
            $goods = Goods::getGoodsById($relation['become_goods_id']);

            if (!empty($goods)) {
                $goods = $goods->toArray();
            } else {
                $goods = [];
            }

        } else {
            $goods = [];
        }
        return view('member.relation', [
            'set' => $relation,
            'goods' => $goods
        ])->render();
    }

    /**
     * 保存关系链数据
     *
     * @return mixed
     */
    public function save()
    {
        $setData = \YunShop::request()->setdata;
        $setData['uniacid'] = \YunShop::app()->uniacid;

        if (empty($setData['become_ordercount'])) {
            $setData['become_ordercount'] = 0;
        }

        if (empty($setData['become_moneycount'])) {
            $setData['become_moneycount'] = 0;
        }

        if (empty($setData['become_goods_id'])) {
            $setData['become_goods_id'] = 0;
        }

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation->setRawAttributes($setData);

            $relation->save();
        } else {
            Relation::create($setData);
        }

        return $this->message('保存成功', yzWebUrl('member.member-relation.index'));
    }

    /**
     * 成为推广员 指定商品查询
     *
     * @return string
     */
    public function query()
    {
        $kwd                = trim(\YunShop::request()->keyword);

        $goods_model= Goods::getGoodsByName($kwd);

        if (!empty($goods_model)) {
            $data = $goods_model->toArray();
        } else {
            $data = [];
        }

        return view('member.goods_query', [
            'goods' => $data
        ])->render();
    }

    /**
     * 会员资格申请列表
     *
     * @return string
     */
    public function apply()
    {
        $starttime = strtotime('-1 month');
        $endtime = time();

        $requestSearch = \YunShop::request()->search;

        if (isset($requestSearch['searchtime']) && $requestSearch['searchtime'] == 1) {
            if ($requestSearch['times']['start'] != '请选择' && $requestSearch['times']['end'] != '请选择') {
                $starttime = strtotime($requestSearch['times']['start']);
                $endtime = strtotime($requestSearch['times']['end']);
            }
        }

        $list = Member::getMembersToApply($requestSearch)
            ->paginate($this->pageSize)
            ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('member.apply', [
            'list' => $list,
            'total' => $list['total'],
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'starttime' => $starttime,
            'endtime' => $endtime,
        ])->render();
    }

    /**
     * 申请协议
     *
     * @return mixed|string
     */
    public function applyProtocol()
    {
        $info = Setting::get("apply_protocol");
        
        $requestProtocol = \YunShop::request()->protocol;
        if($requestProtocol){
            $request = Setting::set('apply_protocol',$requestProtocol);
            if($request){
                return $this->message('保存成功', Url::absoluteWeb('member.member-relation.apply-protocol'));
            }
        }
        
        return view('member.apply-protocol', [
            'info' => $info,
        ])->render();
    }

    /**
     * 检查审核
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chkApply()
    {
        $id = \YunShop::request()->id;

        $member_shop_info_model = MemberShopInfo::getMemberShopInfo($id);

        if ($member_shop_info_model) {
            $member_shop_info_model->is_agent = 1;
            $member_shop_info_model->status = 2;

            if ($member_shop_info_model->save()) {
                return $this->successJson('审核通过');
            } else {
                return $this->errorJson('审核失败');
            }
        } else {
            return $this->errorJson('会员不存在');
        }
    }

    /**
     * 数据导出
     *
     */
    public function export()
    {
        $file_name = date('Ymdhis', time()) . '会员导出';

        $parames = \YunShop::request();
        $list = Member::searchMembers($parames)
            ->get()
            ->toArray();

        $export_data[0] = ['会员ID', '粉丝', '姓名', '手机号', '等级', '分组', '注册时间', '积分', '余额', '订单', '金额', '关注'];

        foreach ($list as $key => $item) {
            if (!empty($item['yz_member']) && !empty($item['yz_member']['group'])) {
                $group = $item['yz_member']['group']['group_name'];

            } else {
                $group = '';
            }

            if (!empty($item['yz_member']) && !empty($item['yz_member']['level'])) {
                $level = $item['yz_member']['level']['level_name'];

            } else {
                $level = '';
            }

            $order = 0;
            $price = 0;

            if (!empty($item['has_one_fans'])) {
                if ($item['has_one_fans']['followed'] == 1) {
                    $fans = '已关注';
                } else {
                    $fans = '未关注';
                }
            } else {
                $fans = '';
            }

            $export_data[$key + 1] = [$item['uid'], $item['nickname'], $item['realname'], $item['mobile'],
                $level, $group, date('YmdHis', $item['createtime']), $item['credit1'], $item['credit2'], $order,
                $price, $fans];
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");

            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });


        })->export('xls');
    }
}