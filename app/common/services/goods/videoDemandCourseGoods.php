<?php
/**
 *  Create date 2018/1/3 14:24
 *  Author: 芸众商城 www.yunzshop.com
 */

namespace app\common\services\goods;

use Setting;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use app\common\components\BaseController;

class VideoDemandCourseGoods extends BaseController
{   
    //视频点播插件设置
    protected $videoDemand;

    public function __construct()
    {
        $this->videoDemand = Setting::get('plugin.video_demand');
    }

    /**
     * 是课程商品的id集合
     * @return [array] [$courseGoods_ids]
     */
    public function courseGoodsIds()
    {
        //是否启用视频点播
        $courseGoods_ids = [];
        if (app('plugins')->isEnabled('video-demand')) {
            if ($this->videoDemand['is_video_demand']) {
                $courseGoods = CourseGoodsModel::getCourseGoodsData();

                foreach ($courseGoods as $value) {
                    $courseGoods_ids[] = $value['goods_id'];
                }

            }
        }

        return $courseGoods_ids;
    }

    /**
     * 商品是否是课程
     * @param  [int]  $goods_id [商品id]
     * @return int    $data 0 不是|1 是
     */
    public function isCourse($goods_id)
    {
        $data = 0;
        if (app('plugins')->isEnabled('video-demand')) {

            if ($this->videoDemand['is_video_demand']) {
                $data = CourseGoodsModel::uniacid()->select('is_course')->where('goods_id', $goods_id)->value('is_course');
            }
        }
        return $data;
    }

}