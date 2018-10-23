<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午5:29
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use Illuminate\Support\Facades\DB;

class ChildenOfMember extends BaseModel
{
    public $table = 'yz_member_children';
    protected $guarded = [];

    public function CreateData($data)
    {
        \Log::debug('----------insert data-----');
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }
}