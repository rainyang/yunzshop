<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\models;


class MemberLevel extends \app\common\models\MemberLevel
{

    protected $hidden = ['goods_id', 'uniacid'];

    /**
     * 获取会员等级信息
     * @return array $data 等级升级的信息
     * 
     */
    public function getLevelData($type)
    {

        $content = ($type == 1) ? 'order_count' : 'order_money';

        $data = self::select('level_name', 'discount', 'freight_reduction', $content)
            ->uniacid()
            ->orderBy('level')
            ->get()->toArray();

        return $data;

    }

      /**
     * 等级升级依据为购买指定商品
     * @return array $data 等级升级的信息
     * 
     */
    public function getLevelGoods()
    {

        $data = self::select('level_name','goods_id', 'discount', 'freight_reduction')->uniacid()
        ->with(['goods' => function($query) {
            return $query->select('id','title','thumb','price');
        }])->orderBy('level')->get()->toArray();

        return $data;
    }

     //模型关联 关联商品
    public function goods()
    {
        return $this->hasOne('app\common\models\Goods', 'id', 'goods_id');
    }

    //关联会员
    public function member()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'level_id', 'id'); //注意yz_member数据表记录和关联的是member_level表的主键id, 而不是level值
    }
}
