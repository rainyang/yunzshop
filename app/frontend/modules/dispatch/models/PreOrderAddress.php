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
use app\frontend\modules\order\models\PreOrder;
use app\frontend\repositories\MemberAddressRepository;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\models\Street;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PreOrderAddress extends OrderAddress
{
    use ValidatesRequests;

    /**
     * @var PreOrder
     */
    public $order;

    /**
     * @param PreOrder $order
     * @throws ShopException
     */
    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $order->setRelation('orderAddress', $this);
        $this->_init();
    }

    /**
     * @throws ShopException
     */
    protected function _init()
    {
        if ($this->order->dispatch_type_id == DispatchType::EXPRESS) {
            $this->fill($this->getOrderAddress()->toArray());
        }
    }

    /**
     * @return OrderAddress
     * @throws ShopException
     */
    protected function getOrderAddress()
    {
        $member_address = $this->getMemberAddress();

        $orderAddress = new OrderAddress();

        $orderAddress->order_id = $this->order->id;

        $orderAddress->mobile = $member_address->mobile;
        $orderAddress->province_id = $member_address->province_id ?: Address::where('areaname', $member_address->province)->value('id');
        $orderAddress->city_id = $member_address->city_id ?: Address::where('areaname', $member_address->city)->where('parentid', $orderAddress->province_id)->value('id');
        $orderAddress->district_id = $member_address->district_id ?: Address::where('areaname', $member_address->district)->where('parentid', $orderAddress->city_id)->value('id');
        $orderAddress->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $member_address->address]);

        if (isset($member_address->street) && $member_address->street != '其他') {
            $orderAddress->street_id = Street::where('areaname', $member_address->street)->where('parentid', $orderAddress->district_id)->value('id');
            if(!isset($orderAddress->street_id)){
                throw new ShopException('收货地址有误请重新保存收货地址');
            }
            $orderAddress->street = $member_address->street;
            $orderAddress->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $orderAddress->street, $member_address->address]);

        } elseif (isset($member_address->street) && $member_address->street != '其他') {
            $orderAddress->street = $member_address->street;
            $orderAddress->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $orderAddress->street, $member_address->address]);
        }

        $orderAddress->realname = $member_address->username;
        $orderAddress->province = $member_address->province;
        $orderAddress->city = $member_address->city;
        $orderAddress->district = $member_address->district;

        return $orderAddress;
    }

    /**
     * 获取用户配送地址模型
     * @return mixed
     * @throws AppException
     */
    public function getMemberAddress()
    {
        $address = json_decode(request()->capture()->input('address', '[]'), true);

        if (count($address)) {
            //$request->input('address');
            $this->validate([
                'address.address' => 'required|string',
                'address.mobile' => 'required|string',
                'address.username' => 'required|string',
                'address.province' => 'required|string',
                'address.city' => 'required|string',
                'address.district' => 'required|string',
            ], ['address' => $address]
            );
            $memberAddress = app(MemberAddressRepository::class)->fill($address);

            return $memberAddress;
        }

        return $this->order->belongsToMember->defaultAddress;
    }

    /**
     * 需要配送
     * @return bool
     */
    private function needDispatch()
    {
        return $this->order->needSend();
    }

    /**
     * 校验参数
     * @param $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @throws AppException
     */
    private function validate($request, array $rules, array $messages = [], array $customAttributes = [])
    {

        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);
        //$validator->errors();
        if ($validator->fails()) {
            throw new AppException($validator->errors()->first());
        }
    }
}