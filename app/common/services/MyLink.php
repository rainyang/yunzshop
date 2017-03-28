<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 下午8:43
 */

namespace app\common\services;

use app\common\facades\Setting;
use app\common\models\Goods;
use YunShop\Article\models\Article;


class MyLink
{
    public static function getMyLink(){
        global $_W;
        //$mylink['designer'] = p('designer');
        /*if ($mylink['designer']) {
            $mylink['diypages'] = pdo_fetchall("SELECT id,pagetype,setdefault,pagename FROM " . tablename('sz_yi_designer') . " WHERE uniacid=:uniacid order by setdefault desc  ", array(':uniacid' => $_W['uniacid']));
        }*/
        $mylink['goods_catgorys'] = \app\backend\modules\goods\models\Category::getAllCategory();

        $mylink['article_setting'] =  Setting::get('plugin.article');

        $mylink['area_count'] = sizeof($mylink['article_setting']['area']);
        if ($mylink['area_count'] == 0){
            //没有设定地区的时候的默认值：
            $mylink['article_setting']['area'][0]['province'] = '';
            $mylink['article_setting']['area'][0]['city'] = '';
            $mylink['area_count'] = 1;
        }
        $mylink['article_categorys'] = \YunShop\Article\models\Category::getCategorys();
        dd($mylink);
    }
}