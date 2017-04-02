<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\ApiController;
use app\common\models\Order;
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends ApiController
{

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::request()->uid;

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                if (!empty($member_info['yz_member'])) {
                    if (!empty($member_info['yz_member']['group'])) {
                        $member_info['group_id'] = $member_info['yz_member']['group']['id'];
                        $member_info['group_name'] = $member_info['yz_member']['group']['group_name'];
                    }

                    if (!empty($member_info['yz_member']['level'])) {
                        $member_info['level_id'] = $member_info['yz_member']['level']['id'];
                        $member_info['level_name'] = $member_info['yz_member']['level']['level_name'];
                    }
                }

                $order_info = Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);

                $member_info['order'] = $order_info;
                return $this->successJson('', $member_info);
            } else {
                return $this->errorJson('用户不存在');
            }

        } else {
            return $this->errorJson('缺少访问参数');
        }

    }

    /**
     * 会员关系链
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMemberRelationInfo()
    {
        $info = MemberRelation::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($info) || empty($member_info)) {
            return $this->errorJson('缺少参数');
        }

        switch ($info['become']) {
           case 2:
               $desc = '<p>本店累计消费满 <span>' . $info['become_ordercount'] . '</span> 次， 才可开启<公众号名称>推广中心，您已消费 <span>0</span> 次，请继续努力</p>';
               break;
           case 3:
               $desc = '<p>本店累计消费满 <span>' . $info['become_moneycount']. '</span>元， 才可开启<公众号名称>推广中心，您已消费 <span>0</span> 元，请继续努力</p>';
               break;
           case 4:
               $desc = '';
               break;
           default:
               $desc = '';
       }

       // TODO 消费和购买指定商品达到条件后 返回审核状态

       $relation = [
           'switch' => $info['status'],
           'become' => $info['become'],
           'become2' => ['total' => $info['become_ordercount'], 'cost' => 0],
           'become3' => ['total' => $info['become_moneycount'], 'cost' => 0],
           'become4' => ['desc' => '商品名'],
           'is_agent' => $member_info['is_agent'],
           'status' => $member_info['status'],
       ];

        return $this->successJson('', $relation);
    }

    /**
     * 会员是否有推广权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function isAgent()
    {
        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($member_info)) {
            return $this->errorJson('会员不存在');
        }

        return $this->successJson('', ['is_agent' => $member_info['is_agent']]);
    }

    /**
     * 会员推广二维码
     *
     * @param $url
     * @param string $extra
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgentQr($url, $extra='')
    {
        if (empty(\YunShop::app()->getMemberId())) {
            return $this->errorJson('请重新登录');
        }

        $url = $url . '&mid=' . \YunShop::app()->getMemberId();

        if (!empty($extra)) {
            $extra = '_' . $extra;
        }

        $extend = 'png';
        $filename = \YunShop::app()->uniacid . '_' . \YunShop::app()->getMemberId() . $extra . '.' . $extend;

        echo QrCode::format($extend)->size(100)->generate($url, storage_path('qr/') . $filename);

        return $this->successJson('', ['qr' => storage_path('qr/') . $filename]);
    }
}