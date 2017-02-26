<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/24
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\model;


class MemberModel
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function getData()
    {
        return $this->data;
    }
}