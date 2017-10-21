<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/4
 * Time: 下午5:00
 */

namespace app\frontend\modules\dispatch\models;


use app\common\models\Address;
use app\common\models\DispatchType;
use app\frontend\models\OrderAddress;
use app\frontend\modules\member\models\MemberAddress;
use app\frontend\modules\order\models\PreOrder;

class PreOrderAddress extends OrderAddress
{
    public $order;
    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $order->setRelation('orderAddress', $this);
        if (!$this->needDispatch()) {
            return;
        }
        $this->_init();
    }

    protected function _init()
    {
        if ($this->order->dispatch_type_id == DispatchType::EXPRESS) {
            $this->fill($this->getAddressByMember());
            //dd($this->validator()->failed());
        }
    }

    protected function getAddressByMember()
    {
        $memberAddress = $this->getMemberAddress();

        $result['address'] = implode(' ', [$memberAddress->province, $memberAddress->city, $memberAddress->district, $memberAddress->address]);
        $result['mobile'] = $memberAddress->mobile;
        list($this->province_id, $this->city_id, $this->district_id) = Address::whereIn('areaname', [$memberAddress->province, $memberAddress->city, $memberAddress->district])->pluck('id');

        $result['address'] = implode(' ', [$memberAddress->province, $memberAddress->city, $memberAddress->district, $memberAddress->address]);
        $result['realname'] = $memberAddress->username;
        return $result;
    }

    /**
     * 获取用户配送地址模型
     * @return MemberAddress
     */
    private function getMemberAddress()
    {
        $request = \Request::capture();
        $address = json_decode($request->input('address', '[]'), true);

        if (count($address)) {
            //$request->input('address');
//            $this->validate([
//                'address.address' => 'required|string',
//                'address.mobile' => 'required|string',
//                'address.username' => 'required|string',
//                'address.province' => 'required|string',
//                'address.city' => 'required|string',
//                'address.district' => 'required|string',
//            ], ['address' => $address]
//            );
            return new MemberAddress($address);
        }

        return $this->order->belongsToMember->defaultAddress;
    }

    /**
     * 需要配送
     * @return bool
     */
    private function needDispatch()
    {
        if ($this->order->is_virtual) {
            return false;
        }
        return true;
    }
}