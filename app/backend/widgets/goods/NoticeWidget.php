<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Notice;
use app\common\models\Member;

class NoticeWidget extends Widget
{

    public function run()
    {
        $noticetype = [];
        $saler = [];
        $uid = '';
        $notices = Notice::getList($this->goods_id);
        if ($notices) {
            foreach ($notices as $notice) {
                $noticetype[] = $notice['type'];
                $uid = $notice['uid'];
            }
            $saler = Member::getMemberById($uid);
        }
        return $this->render('goods/notice/notice', [
            'uid'=>$uid,
            'noticetype'=>$noticetype,
            'saler'=>$saler
        ]);
    }
}