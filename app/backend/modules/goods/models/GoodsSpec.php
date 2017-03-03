<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 下午18:16
 */
namespace app\backend\modules\goods\models;

use \app\common\models\GoodsSpecItem;

class GoodsSpec extends \app\common\models\GoodsSpec
{
    public static function saveSpec($specPost, $goods_id = 2)
    {
        $data['type'] = 1; //todo, 需要传递商品信息进来
        $spec_ids = $specPost->spec_id;
        //dd($spec_ids);
        $spec_titles = $specPost->spec_title;
        $specids = [];
        $spenLen = count($spec_ids);
        $specids = [];
        $spec_items = [];
        for ($specIndex = 0; $specIndex < $spenLen; $specIndex++) {
            $spec_id = "";
            $get_spec_id = $spec_ids[$specIndex];
            $spec = array(
                "uniacid" => \YunShop::app()->uniacid,
                "goods_id" => $goods_id,
                "display_order" => $specIndex,
                "title" => $spec_titles[$get_spec_id]
            );
            if (is_numeric($get_spec_id)) {
                parent::updateOrCreate(['id' => $get_spec_id], $spec);
                $spec_id = $get_spec_id;
            } else {
                $goods_spec = parent::Create($spec);
                //dd($goods_spec);
                $spec_id = $goods_spec->id;
            }

            $spec_ids_attr = "spec_item_id_" . $get_spec_id;
            $spec_item_titles_attr = " spec_item_title_" . $get_spec_id;
            $spec_item_shows_attr = "spec_item_show_" . $get_spec_id;
            $spec_item_thumbs_attr = "spec_item_thumb_" . $get_spec_id;
            $spec_item_oldthumbs_attr = "spec_item_oldthumb_" . $get_spec_id;
            $spec_item_virtuals_attr = "spec_item_virtual_" . $get_spec_id;

            $spec_item_ids = $specPost->$spec_ids_attr;
            $spec_item_titles = $specPost->$spec_item_titles_attr;
            $spec_item_shows = $specPost->$spec_item_shows_attr;
            $spec_item_thumbs = $specPost->$spec_item_thumbs_attr;
            $spec_item_oldthumbs = $specPost->$spec_item_oldthumbs_attr;
            $spec_item_virtuals = $specPost->$spec_item_virtuals_attr;
            $itemlen = count($spec_item_ids);
            $itemids = [];
            for ($n = 0; $n < $itemlen; $n++) {
                $item_id = "";
                $get_item_id = $spec_item_ids[$n];
                $specItem = [
                    "uniacid" => \YunShop::app()->uniacid,
                    "specid" => $spec_id,
                    "display_order" => $n,
                    "title" => $spec_item_titles[$n],
                    "show" => $spec_item_shows[$n],
                    "thumb" => save_media($spec_item_thumbs[$n]),
                    "virtual" => $data['type'] == 3 ? $spec_item_virtuals[$n] : 0
                ];
                //$f = "spec_item_thumb_" . $get_item_id;
                if (is_numeric($get_item_id)) {
                    GoodsSpecItem::updateOrCreate(['id' => $get_item_id], $specItem);
                    /*pdo_update("sz_yi_goods_spec_item", $specItem, array(
                        "id" => $get_item_id
                    ));*/
                    $item_id = $get_item_id;
                } else {
                    $goods_spec_item = GoodsSpecItem::Create($specItem);
                    $item_id = $goods_spec_item->id;
                    //pdo_insert('sz_yi_goods_spec_item', $specItem);
                }
                $itemids[] = $item_id;
                $specItem['get_id'] = $get_item_id;
                $specItem['id'] = $item_id;
                $spec_items[] = $specItem;
            }
            if (count($itemids) > 0) {
                GoodsSpecItem::where('specid', '=', $spec_id)->whereNotIn('id', $itemids )->delete();
            } else {
                GoodsSpecItem::where('specid', '=', $spec_id)->delete();
            }
            parent::updateOrCreate(['id' => $spec_id], ['content' => serialize($itemids)]);
            $specids[] = $spec_id;
        }
        if (count($specids) > 0) {
            parent::where('goods_id', '=', $goods_id)->whereNotIn('id', $specids )->delete();
        } else {
            parent::where('goods_id', '=', $goods_id)->delete();
        }
    }
}