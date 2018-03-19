<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\models\MemberLevel;
use app\common\services\goods\LeaseToyGoods;
use  Yunshop\LeaseToy\models\LevelRightsModel;

class MemberLevelController extends ApiController
{

    protected $settinglevel;
    
    public function __construct()
    {
        parent::__construct();
        //会员等级的升级的规则
        $this->settinglevel = \Setting::get('shop.member');
    }


    /**
     * 等级信息
     * @return json 
     */
    public function index()
    {
        //会员等级的升级的规则
        $this->settinglevel = \Setting::get('shop.member');

        if (!$this->settinglevel) {
            return $this->errorJson('未进行等级设置');
        }
        //升级条件判断
        if ($this->settinglevel['level_type'] == 2) {
            $data =  MemberLevel::getLevelGoods();
            $bool = LeaseToyGoods::whetherEnabled();
            //商品图片处理
            foreach ($data as &$value) {
                $value['rent_free'] = 0;
                $value['deposit_free'] = 0;
                if ($bool) {
                    $levelRights = LevelRightsModel::getRights($value['id']);
                    if ($levelRights) {
                        $value['rent_free'] = $levelRights->rent_free;
                        $value['deposit_free'] = $levelRights->deposit_free;
                    }
                }
                $value['goods']['thumb'] = replace_yunshop(yz_tomedia($value['goods']['thumb']));
            }
        } else {
            $data = MemberLevel::getLevelData($this->settinglevel['level_type']);
        }

        $levelData = [
            'level_type' => $this->settinglevel['level_type'],
            'data' => $data
        ];
// dd($levelData);
        return $this->successJson('ok',$levelData);
    }

    /**
     * 会员升级详情
     * @return [json] [detail]
     */
    public function upgradeDetail()
    {
        $id = intval(\YunShop::request()->id);

        if (!$id) {
            return $this->errorJson('参数无效');
        }

        if ($this->settinglevel['level_type'] == 2) {
            $detail = MemberLevel::uniacid()
                    ->with(['goods' => function($query) {
                        return $query->select('id', 'title', 'thumb', 'price');
                    }])->find($id);
            $detail->goods->thumb = replace_yunshop(yz_tomedia($detail->goods->thumb));
        } else {
            $detail = MemberLevel::uniacid()->find($id);
        }

        $detail->level_type = $this->settinglevel['level_type'];


        // dd($detail->toArray());
        return $this->successJson('leveldetail', $detail);
    }
}

