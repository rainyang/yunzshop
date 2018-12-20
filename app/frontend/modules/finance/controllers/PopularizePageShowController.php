<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 14:37
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\BaseController;

class PopularizePageShowController extends BaseController
{


    public function index()
    {
        $all_set =  \Setting::getByGroup("popularize");
        $data = [
            'wechat' => [
                'vue_route' => !empty($all_set['wechat']['vue_route'])?$all_set['wechat']['vue_route']:[],
                'url' => !empty($all_set['wechat']['callback_url'])?$all_set['wechat']['callback_url']:'',
            ],
            'mini' => [
                'vue_route' => !empty($all_set['mini']['vue_route'])?$all_set['mini']['vue_route']:[],
                'url' => !empty($all_set['mini']['callback_url'])?$all_set['mini']['callback_url']:'',
            ],
            'wap' => [
                'vue_route' => !empty($all_set['wap']['vue_route'])?$all_set['wap']['vue_route']:[],
                'url' => !empty($all_set['wap']['callback_url'])?$all_set['wap']['callback_url']:'',
            ],
            'app' => [
                'vue_route' => !empty($all_set['app']['vue_route'])?$all_set['app']['vue_route']:[],
                'url' => !empty($all_set['app']['callback_url'])?$all_set['app']['callback_url']:'',
            ],
            'alipay' => [
                'vue_route' => !empty($all_set['alipay']['vue_route'])?$all_set['alipay']['vue_route']:[],
                'url' => !empty($all_set['alipay']['callback_url'])?$all_set['alipay']['callback_url']:'',
            ],
        ];

        $this->successJson('成功',$data);
    }
}