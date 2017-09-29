<?php
namespace app\frontend\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\frontend\models\Member;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 上午11:57
 */
class SettingController extends BaseController
{
    /**
     * 商城设置接口
     * @param string $key  setting表key字段值
     * @return
     */
    public function get()
    {
        $key = \YunShop::request()->setting_key ? \YunShop::request()->setting_key : 'shop';
        if (!empty($key)) {
            $setting = Setting::get('shop.' . $key);
        } else {
            $setting = Setting::get('shop');
        }

        if (!$setting) {
            return $this->errorJson('未进行设置.');
        }

        //增加商品详情显示爱心值 2017-09-29
        $setting['goods_detail_show_love'] = Setting::get('love.goods_detail_show_love') ? true : false;

        $setting['logo'] = replace_yunshop(tomedia($setting['logo']));

        $relation = MemberRelation::getSetInfo()->first();

        if ($relation) {
            $setting['agent'] = $relation->status ? true : false;
        } else {
            $setting['agent'] = false;
        }

        //强制绑定手机号
        $member_set = Setting::get('shop.member');

        if ((1 == $member_set['is_bind_mobile']) && \YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
            $member_model = Member::getMemberById(\YunShop::app()->getMemberId());

            if ($member_model && $member_model->mobile) {
                $setting['is_bind_mobile'] = 0;
            } else {
                $setting['is_bind_mobile'] = 1;
            }
        } else {
            $setting['is_bind_mobile'] = 0;
        }

        return $this->successJson('获取商城设置成功', $setting);

    }

}