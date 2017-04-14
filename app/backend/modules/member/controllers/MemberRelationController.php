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

    public function index()
    {

        $relation = Relation::getSetInfo()->first();

        if (!empty($relation)) {
            $relation = $relation->toArray();
        }

        if (!empty($relation['become_goods_id'])) {
            $goods = Goods::getGoodsById($relation['become_goods_id']);
            $goods = $goods->toArray();
        } else {
            $goods = [];
        }
        return view('member.relation', [
            'set' => $relation,
            'goods' => $goods
        ])->render();
    }

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
            $relation = Relation::create($setData);
        }

        return $this->message('保存成功', yzWebUrl('member.member-relation.index'));
    }

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

    public function apply()
    {
        $starttime = strtotime('-1 month');
        $endtime = time();

        $requestSearch = \YunShop::request()->search;


        if($requestSearch){

            if ($requestSearch['searchtime']) {
                if ($requestSearch['times']['start'] != '请选择' && $requestSearch['times']['end'] != '请选择') {
                    $requestSearch['times']['start'] = strtotime($requestSearch['times']['start']);
                    $requestSearch['times']['end'] = strtotime($requestSearch['times']['end']);
                    $starttime = strtotime($requestSearch['times']['start']);
                    $endtime = strtotime($requestSearch['times']['end']);
                }else{
                    $requestSearch['times'] = '';
                }
            }else{
                $requestSearch['times'] = '';
            }

            $requestSearch = array_filter($requestSearch, function ($item) {
                return $item !== '';// && $item !== 0;
            });
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
}