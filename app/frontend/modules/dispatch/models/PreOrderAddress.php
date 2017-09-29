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
use app\frontend\modules\order\models\PreGeneratedOrder;

class PreOrderAddress extends OrderAddress
{
    public $order;
    public function setOrder(PreGeneratedOrder $order)
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
            dd($this->validator()->failed());
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
    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'address'=> '收货详细地址',
            'mobile'=> '收货电话',
            'realname'=> '收货人姓名',
            'province_id'=> '收货省份',
            'city_id'=> '收货城市',
            'district_id'=> '收货地区',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        $rule =  [
            //具体unique可看文档 https://laravel.com/docs/5.4/validation#rule-unique
            'address'=> 'required',
            'mobile'=> 'required',
            'realname'=> 'required',
            'province_id'=> 'required',
            'city_id'=> 'required',
            'district_id'=> 'required',
        ];

        return $rule;
    }
}