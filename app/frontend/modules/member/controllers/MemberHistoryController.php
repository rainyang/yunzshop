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
        $member_id = 62;
        $uniacid = 6;

        //$history = new MemberHistory();
        //$list = $history->getMemberHistoryList($member_id);
        $list = MemberHistory::getMemberHistoryList($member_id, $uniacid);

        //echo '<pre>'; print_r($list); exit;
        $this->render('mobile/member/cart', ['a' => 'entry']);
    }
}