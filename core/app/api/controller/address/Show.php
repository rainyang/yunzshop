<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Show extends YZ
{
    /**
     * 获取用户的收货地址信息
     * @return json [用户的所有收货地址信息]
     */
    public function index()
    {
        $openid = m('user')->isLogin();

        $sql = 'SELECT id, uniacid, openid, realname, mobile, province, city, area, address, isdefault, zipcode, street FROM ' . tablename('sz_yi_member_address') . ' WHERE openid = "'.$openid.'" and deleted = 0';

        $data = pdo_fetchall($sql);

        if (empty($data)) {
            return $this->returnError('该用户尚未添加收货地址');
        } else {
            return $this->returnSuccess($data);
        }
        // ddump($data);
    }
}