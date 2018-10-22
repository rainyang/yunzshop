<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午3:50
 */

namespace app\Jobs;


use app\backend\modules\member\models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class memberParentOfMemberJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;
    private $member_info;

    public function __construct($uniacid, $member_info)
    {
        $this->uniacid = $uniacid;
        $this->member_info = $member_info->toArray();
    }

    public function handle()
    {
        \Log::debug('-----queque uniacid-----', $this->uniacid);
        return $this->synRun($this->uniacid, $this->member_info);
    }

    public function synRun($uniacid, $memberInfo)
    {
        $memberInfo = new Member();

        $data = $memberInfo->getTreeAllNodes($uniacid);
        \Log::debug('--------queue data count-----', $data->cout());
        //$data = $memberInfo->getDescendants($uniacid, 65);
    }
}