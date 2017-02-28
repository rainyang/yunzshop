<?php
/**
 * Created by PhpStorm.
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;
use app\common\models\GoodsParam;

class Goods extends Model
{
    public $table = 'yz_goods';

    //public $fillable = ['display_order'];

    public $guarded = [];

    public static function getList()
    {
        return parent::get();
    }

    public static function getGoodsById($id)
    {
        return parent::find($id);
    }

    public function hasManyParams()
    {
        return $this->hasMany('app\common\models\GoodsParam');
    }
}