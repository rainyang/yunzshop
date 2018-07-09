<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/7/6
 * Time: 下午3:24
 */

namespace app\Jobs;


use app\common\models\Member;
use app\common\models\MemberShopInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Poster\models\PosterQrcode;
use Yunshop\Poster\models\Qrcode;

class scanPostConcernQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 处理事件
     *
     * @var
     */
    protected $postProcessor;

    /**
     * 当前公众号
     *
     * @var
     */
    protected $uniacid;

    /**
     * 扫码关注者
     *
     * @var
     */
    protected $from;

    /**
     * 海报用户
     *
     * @var
     */
    protected $to;

    /**
     * 海报消息
     *
     * @var
     */
    protected $msg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uniacid, $postProcessor)
    {
        $this->uniacid        = $uniacid;
        $this->postProcessor  = $postProcessor;

        $this->msg            = $this->postProcessor->message;
        $this->from           = $this->postProcessor->message['fromusername'];
        $this->to             = $this->postProcessor->message['eventkey'];

        \Log::debug('------poster processor message----', [$this->postProcessor]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug('-------scan poster from-----', [$this->from]);
        \Log::debug('-------scan poster to-------', [$this->to]);

        //TODO $from关注者用户是否存在，存在验证上线
        $from_member_model = MemberShopInfo::getMemberShopInfoByOpenid($this->from);
        if (!is_null($from_member_model)) {
            \Log::debug('--------poster member is not null------');
            $from_member_id = $from_member_model->member_id;
            $from_parent_id = $from_member_model->parent_id;
            \Log::debug('------poster member id----', [$from_member_id]);
            \Log::debug('------poster parent id----', [$from_parent_id]);

            //TODO $to海报用户信息
            \Log::debug('------poster handle processor message----', [$this->msg]);
            $qrcodeId = $this->getPosterForUser($this->postProcessor->message);
            \Log::debug('------poster qrcodeId-----', [$qrcodeId]);

            $to_member_id = PosterQrcode::getRecommenderIdByQrcodeId($qrcodeId);

            if (!empty($to_member_id) && 0 == $from_parent_id) {
                //TODO $from->parent_id 是否为0，是0改为$to->uid
                $from_member_model->parent_id = $to_member_id;

                \Log::debug('------poster modify parent_id----');
                $from_member_model->save();

                //TODO 分销-会员关系链
                Member::createRealtion($from_member_id, $to_member_id);
            }
        } else {
            \Log::debug('-----poster member is null by openid-----', [$this->from]);
        }
    }

    private function getPosterForUser($msg)
    {
        \Log::debug('-----poster msg-----', [$msg]);
        $msgEvent = strtolower($msg['event']);
        $msgEventKey = strtolower($msg['eventkey']);

        if ($msgEvent == 'scan') {
            $scene = $msgEventKey;
        } else {
            //如果用户之前未关注，进行关注后推送的 Event 是 "subscribe",
            //推送的 EventKey 是以 "qrscene_" 为前缀，后面跟着二维码的参数值.
            //因为需求中提到存在这种情况 -- "尽管之前已经关注,但还不是商城的会员",
            //所以这里并不根据 Event 类型来判别是否是会员, 只是识别出二维码的特征值(场景值/场景字符串), 用于定位二维码 ID
            $scene = substr($msgEventKey, strpos($msgEventKey, '_') + 1);
        }

        if (is_int($scene) && ($scene != 0)) { //临时二维码
            $sceneId = $scene;
            $qrcode = Qrcode::getQrcodeBySceneId($sceneId);
        } else { //永久二维码
            $sceneStr = $scene;
            $qrcode = Qrcode::getForeverQrcodeBySceneStr($sceneStr);
        }

        return $qrcode->id;
    }
}