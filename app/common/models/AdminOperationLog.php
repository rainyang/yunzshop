<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/14
 * Time: 上午10:18
 */

namespace app\common\models;


class AdminOperationLog extends BaseModel
{
    protected $table = 'yz_admin_operation_log';
    protected $casts = [
        'after' => 'json',
        'before' => 'json',
    ];

    public function save(array $options = [])
    {
        $this->ip = request()->ip();
        $this->admin = substr(isset(\YunShop::app()->username) ? \YunShop::app()->username : '',0,20);

        return parent::save($options);
    }
}