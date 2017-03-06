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
    public $goodsId = '';

    public function run()
    {
        $noticetype = [];
        $salerMember = new Member;
        $notice = Notice::getList($this->goodsId);
        if ($notice) {
            $noticetype = explode(',', $notice->type);
            $saler = Member::getMemberById($notice->uid);
            $salerMember->setRawAttributes($saler);
        }
        
        return $this->render('goods/notice/notice', [
            'item'=>$notice,
            'noticetype'=>$noticetype,
            'saler'=>$salerMember
        ]);
    }
}