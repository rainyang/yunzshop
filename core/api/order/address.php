<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\order;
class address extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
    }
    function save(){
        $province = $_GPC["province"];
        $realname = $_GPC["realname"];
        $mobile = $_GPC["mobile"];
        $city = $_GPC["city"];
        $area = $_GPC["area"];
        $address = trim($_GPC["address"]);
        $id = intval($_GPC["id"]);
        if (!empty($id))
        {
            if (empty($realname))
            {
                $ret = "请填写收件人姓名！";
                show_json(0, $ret);
            }

            if (empty($mobile)) {
                $ret = "请填写收件人手机！";
                show_json(0, $ret);
            }
            if ($province == "请选择省份") {
                $ret = "请选择省份！";
                show_json(0, $ret);
            }
            if (empty($address)) {
                $ret = "请填写详细地址！";
                show_json(0, $ret);
            }
            $item = pdo_fetch("SELECT address FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
                ":id" => $id,
                ":uniacid" => $_W["uniacid"]
            ));

            $address_array = iunserializer($item["address"]);
            $address_array["realname"] = $realname;
            $address_array["mobile"] = $mobile;
            $address_array["province"] = $province;
            $address_array["city"] = $city;
            $address_array["area"] = $area;
            $address_array["address"] = $address;
            $address_array = iserializer($address_array);
            pdo_update("sz_yi_order", array(
                "address" => $address_array
            ), array(
                "id" => $id,
                "uniacid" => $_W["uniacid"]
            ));
            $ret = "修改成功";
            show_json(1, $ret);
        } else {
            $ret = "Url参数错误！请重试！";
            show_json(0, $ret);
        }
    }
}