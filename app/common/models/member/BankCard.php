<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:21
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\models\member;


use app\common\models\BaseModel;

class BankCard extends BaseModel
{
    protected $table = 'yz_member_bank_card';

    protected $guarded = [''];


    public function member()
    {
        return $this->belongsTo('app\common\models\Member', 'member_id', 'uid');
    }



}
