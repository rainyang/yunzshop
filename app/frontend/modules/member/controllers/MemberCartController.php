<?php
namespace app\frontend\modules\member\controllers;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberCart;

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:17
 */
class MemberCartController extends BaseController
{
    public function index()
    {
        $memberId = '1';
        $list = MemberCart::getMemberCartList($memberId);
        $data = array(
            'result'    => '1',
            'msg'       => '',
            'data'      => $list
        );
        echo json_encode($data);
        exit;
    }

    public function store()
    {
        $data = array(
            'member_id' => '77',
            'uniacid'   => '8',
            'goods_id'  => '19',
            'total'     => '1',
            'price'     => '100',
            'option_id' => '123'
        );

        $result = MemberCart::storeGoodsToMemberCart($data);
        dd($result);
    }
    public function destroy()
    {
        $cartId = '2';
        $result = MemberCart::destroyGoodsToMemberCartById($cartId);
        dd($result);
    }

}
