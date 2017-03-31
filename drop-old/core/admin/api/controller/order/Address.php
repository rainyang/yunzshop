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
namespace admin\api\controller\order;
class Address extends \admin\api\YZ
{
    public function __construct()
    {
        parent::__construct();
    }
    function save(){
        $para = $this->getPara();
        $province = $para["province"];
        $realname = $para["realname"];
        $mobile = $para["mobile"];
        $city = $para["city"];
        $area = $para["area"];
        $address = trim($para["address"]);
        $id = intval($para["order_id"]);
        if (!empty($id))
        {
            if (empty($realname))
            {
                $this->returnError('请填写收件人姓名！');
            }
            if (empty($mobile)) {
                $this->returnError('请填写收件人手机！');
            }
            if ($province == "请选择省份") {
                $this->returnError('请选择省份！');
            }
            if (empty($address)) {
                $this->returnError('请填写详细地址！');
            }
            $item = pdo_fetch("SELECT address FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
                ":id" => $id,
                ":uniacid" => $para["uniacid"]
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
                "uniacid" => $para["uniacid"]
            ));
            $this->returnSuccess($para,'修改成功');
        } else {
            $this->returnError('Url参数错误！请重试！');
        }
    }
}