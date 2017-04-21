<?php
namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\backend\modules\coupon\models\Coupon;
use app\common\helpers\PaginationHelper;
use app\common\models\MemberCoupon;
use app\common\helpers\Url;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/20
 * Time: 16:20
 */
class CouponController extends BaseController
{
    //优惠券列表
    public function index()
    {
        $keyword = \YunShop::request()->keyword;
        $getType = \YunShop::request()->gettype;
        $searchSearchSwitch = \YunShop::request()->timesearchswtich;
        $timeStart = strtotime(\YunShop::request()->time['start']);
        $timeEnd = strtotime(\YunShop::request()->time['end']);

        $pageSize = 10;
        if (empty($keyword) && empty($getType) && ($searchSearchSwitch == 0)){
            $list = Coupon::uniacid()->orderBy('display_order','desc')->paginate($pageSize)->toArray();
        } else {
//            dd($timeStart);exit;
//            dd($timeEnd);exit;
            $list = Coupon::getCouponsBySearch($keyword, $getType, $searchSearchSwitch, $timeStart, $timeEnd)
                        ->orderBy('display_order','desc')
                        ->paginate($pageSize)
                        ->toArray();
        }
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        foreach($list['data'] as &$item){
            $item['gettotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->count();
            $item['usetotal'] =  MemberCoupon::uniacid()->where("coupon_id", $item['id'])->where("used", 1)->count();
            $item['lasttotal'] = $item['total'] - $item['gettotal'];
        }

        return view('coupon.index', [
            'list' => $list['data'],
            'pager' => $pager,
        ])->render();
    }

    //添加优惠券
    public function create()
    {

        //获取表单提交的值
        $couponRequest = \YunShop::request()->coupon;

        //表单验证
        if($couponRequest){
            $coupon = new Coupon();
            $coupon->uniacid = \YunShop::app()->uniacid;
            $coupon->time_start = strtotime(\YunShop::request()->time['start']);
            $coupon->time_end = strtotime(\YunShop::request()->time['end']);
            $coupon->use_type =\YunShop::request()->usetype;

            $coupon->fill($couponRequest);
            $validator = $coupon->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            } elseif($coupon->save()) {
                return $this->message('优惠券创建成功', Url::absoluteWeb('coupon.coupon.index'));
            } else{
                $this->error('优惠券创建失败');
            }
        }

        return view('coupon.coupon', [
            'coupon' => $couponRequest,
        ])->render();
    }

    //编辑优惠券
    public function edit()
    {
        $coupon_id = intval(\YunShop::request()->id);
        if (!$coupon_id) {
            $this->error('请传入正确参数.');
        }

        $coupon = Coupon::getCouponById($coupon_id)->first();
        $couponRequest = \YunShop::request()->coupon;
        if ($couponRequest) {

            $couponRequest['time_start'] =strtotime(\YunShop::request()->time['start']);
            $couponRequest['time_end'] =strtotime(\YunShop::request()->time['end']);

            //todo 表单验证
            $coupon->fill($couponRequest);
            $validator = $coupon->validator();
            if($validator->fails()){
                $this->error($validator->messages());
            } else{
                if($coupon->save()){
                    return $this->message('优惠券修改成功', Url::absoluteWeb('coupon.coupon.index'));
                } else{
                    $this->error('优惠券修改失败');
                }
            }
        }

        return view('coupon.coupon', [
            'coupon' => $coupon,
        ])->render();
    }

    //删除优惠券
    public function destory()
    {
        $coupon_id = intval(\YunShop::request()->id);
        if (!$coupon_id) {
            $this->error('请传入正确参数.');
        }

        $coupon = Coupon::getCouponById($coupon_id);
        if (!($coupon->first())) {  //空collection
            return $this->message('无此记录或者已被删除.', '', 'error');
        }

        $usageCount = Coupon::getUsageCount($coupon_id)->first()->toArray();
        if($usageCount['has_many_member_coupon_count'] > 0){
            return $this->message('优惠券已被领取且尚未使用,因此无法删除', Url::absoluteWeb('coupon.coupon'), 'error');
        }

        $res = $coupon->delete();
        if ($res) {
            return $this->message('删除优惠券成功', Url::absoluteWeb('coupon.coupon'));
        } else {
            return $this->message('删除优惠券失败', '', 'error');
        }
    }


    /**
     * 获取搜索优惠券
     * @return html
     */
    public function getSearchCoupons()
    {
        $keyword = \YunShop::request()->keyword;
        $coupons = Coupon::getCouponsByName($keyword);
        return view('coupon.query', [
            'coupons' => $coupons
        ])->render();
    }

    //用于"适用范围"添加商品或者分类
    public function addParam()
    {
        $type = \YunShop::request()->type;
        switch($type){
            case 'goods':
                return view('coupon.tpl.goods')->render();
                break;
            case 'category':
                return view('coupon.tpl.category')->render();
                break;
        }
    }

}