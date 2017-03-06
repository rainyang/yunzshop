<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午8:34
 */

namespace app\frontend\modules\member\models;


class MemberAddress extends \app\common\models\MemberAddress
{
    /*
     *  Get a list of members receiving addresses
     *
     *  @param int $memberId
     *
     *  @return array
     * */
    public static function getAddressList($memberId)
    {
        return static::select('id', 'username', 'zipcode', 'province', 'city', 'district', 'address', 'isdefault')
            ->uniacid()->where('uid', $memberId)->get()->toArray();
    }
    /*
     *  Get the receiving address information through the receiving address ID
     *
     *  @param int $addressId
     *
     *  @return array
     * */
    public static function getAddressById($addressId)
    {
        return static::where('id', $addressId)->first();
    }
    /*
     *  Delete the receiving address by receiving address ID
     *
     *  @param int $addressId
     *
     *  @return int 0 or 1
     * */
    public static function destroyAddress($addressId)
    {
        return static::where('id', $addressId)->delete();
    }
    /*
     *  Cancel the default address
     *
     *  @param int $memberId
     *
     *  @return int 0or 1
     * */
    public static function cancelDefaultAddress($memberId)
    {
        return static::uniacid()->where('uid', $memberId)->where('isdefault', '1')->update('isdefault', '0');
    }

}