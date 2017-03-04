<?php

namespace app\backend\modules\goods\observers;

use app\backend\modules\goods\models\Discount;
use app\backend\modules\goods\models\Share;
use app\backend\modules\goods\services\DiscountService;
use app\backend\modules\goods\services\Privilege;
use app\backend\modules\goods\services\PrivilegeService;


/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class GoodsObserver extends \app\common\observers\BaseObserver
{

    public function __construct($model)
    {
        $model->share['goods_id'] = $model->goodsId;
        $model->privilege['goods_id'] = $model->goodsId;
        $model->discount['goods_id'] = $model->goodsId;
    }

    public function creating(Eloquent $model)
    {
        if ($model->share) {
            return Share::validator($model->share);
        }
        if ($model->privilege) {
            $model->privilege['show_levels'] = PrivilegeService::arrayToSting($model->privilege['show_levels']);
            return Privilege::validator($model->privilege);
        }
        if ($model->discount) {
            return Discount::validator($model->discount);
        }
    }

    public function created(Eloquent $model)
    {
        if ($model->share) {
            Share::createdShare($model->share);
        }
        if ($model->privilege) {
            $model->privilege['show_levels'] = PrivilegeService::stringToArray($model->privilege['show_levels']);
            Privilege::createdPrivilege($model->privilege);
        }
        if ($model->discount) {
            $discounts = DiscountService::resetArray($model->discount);
            foreach ($discounts as $discount) {
                Discount::createdDiscount($discount);
            }
        }
    }

    public function updating(Eloquent $model)
    {
        if ($model->share) {
            return Share::validator($model->share);
        }
        if ($model->privilege) {
            $model->privilege['show_levels'] = PrivilegeService::arrayToSting($model->privilege['show_levels']);
            return Privilege::validator($model->privilege);
        }
        if ($model->discount) {
            return Discount::validator($model->discount);
        }
    }

    public function updated(Eloquent $model)
    {
        if ($model->share) {
            Share::updatedShare($model->share);
        }
        if ($model->privilege) {
            $model->privilege['show_levels'] = PrivilegeService::stringToArray($model->privilege['show_levels']);
            Privilege::updatedPrivilege($model->privilege);
        }
        if ($model->discount) {
            Discount::deletedDiscount($model->goodsId);
            $discounts = DiscountService::resetArray($model->discount);
            foreach ($discounts as $discount) {
                Discount::createdDiscount($discount);
            }

        }
    }

    public function deleted(Eloquent $model)
    {
        if (!empty(Share::getInfo($model->goodsId))) {
            Share::deletedShare($model->goodsId);
        }
        if (!empty(Privilege::getInfo($model->goodsId))) {
            Privilege::deletedPrivilege($model->goodsId);
        }
        if (!empty(Discount::getInfo($model->goodsId))) {
            Discount::deletedDiscount($model->goodsId);
        }
    }
}