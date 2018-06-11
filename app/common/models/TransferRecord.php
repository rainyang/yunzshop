<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午4:15
 */

namespace app\common\models;

/**
 * Class TransferRecord
 * @package app\common\models
 * @property string report_url
 * @property int order_pay_id
 */
class TransferRecord extends BaseModel
{
    public $table = 'yz_transfer_record';

}