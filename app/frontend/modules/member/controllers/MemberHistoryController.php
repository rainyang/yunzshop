<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:26
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberHistory;


class MemberHistoryController extends BaseController
{
    public function index()
    {
        $memberId = 96;
        $uniacid = 8;


        $list = MemberHistory::getMemberHistoryList($memberId, $uniacid);


        echo '<pre>'; print_r($list); exit;
    }
    /**
     * 添加浏览记录【增】
     * @Author::yitian 2017-03-01 qq:751818588
     * @access public
     * @param int $memberId 会员ID
     * @param int $goodsId 商品ID
     **/
    public function create()
    {
        $memberId = 96;
        $goodsId = 100;
        $result = MemberHistory::saveMemberHistory($memberId, $goodsId);

        dd($result);
    }
}