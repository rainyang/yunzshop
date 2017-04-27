<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/24
 * Time: 上午11:13
 */

namespace app\common\helpers;


use app\common\components\BaseController;
use app\common\models\Address;

class AddressHelper extends BaseController
{

    public static function tplLinkedAddress($names, $data)
    {
        $html = "";
        $provinceData = Address::getProvince();
        
        echo "<pre>"; print_r($provinceData);exit;
        
//        foreach ($names as $key=>$item) {
//            if($key == '0'){
//                $html .= '<select id="sel-provance" name="'.$item.'"  class="select">';
//
//                $html .= '<option value="" selected="true">请选择</option>';
//
//                $html .= '</select>';
//            }else {
//                $html .= '<select id="sel-provance" name="'.$item.'"  class="select">';
//                $html .= '<option value="" selected="true">请选择</option>';
//                $html .= '</select>';
//            }
//
//        }




        return $html;
    }

}