<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\models\MemberLevel;

class MemberLevelController extends ApiController
{

    /**
     * 等级信息
     * @return json 
     */
    public function index()
    {
        //会员等级的升级的规则
        $settinglevel = \Setting::get('shop.member');

        if (!$settinglevel) {
            return $this->errorJson('未进行等级设置');
        }

        $data = MemberLevel::getLevelData($settinglevel['level_type']);

        $this->successJson('ok',$data);
    }
}

