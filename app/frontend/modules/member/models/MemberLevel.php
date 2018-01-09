<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\models;

use app\backend\modules\member\models\MemberLevel as backendMemberLevel;

class MemberLevel extends backendMemberLevel
{

    protected $hidden = ['goods_id'];

    /**
     * 获取会员等级信息
     * @param  string $type 会员等级依据
     * @return array $data 各等级升级的信息
     * 
     */
    public function getLevelData($type)
    {

        $data['level_type'] = $type;

        if ($type == 2) {

            $data['data'] = self::select('level_name','goods_id')->uniacid()
            ->with(['goods' => function($query) {
                return $query->select('id','title','thumb','price');
            }])->orderBy('level')->get()->toArray();

            //处理图片地址
            foreach ($data['data'] as &$value) {
                $value['goods']['thumb'] = replace_yunshop(tomedia($value['goods']['thumb']));     
            }
            return $data;
        }

        $content = $type == 1 ? 'order_count' : 'order_money';

        $data['data'] = self::select('level_name', $content)
            ->uniacid()
            ->orderBy('level')
            ->get()->toArray();

        return $data;

    }
}
