<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: RainYang
 * Date: 2017/2/22
 * Time: 19:35
=======
 * User: yanglei
 * Date: 2017/2/28
 * Time: 上午11:31
>>>>>>> 8cd399a5a5fe4f2aecc9117c987f889cb5350423
 */

namespace app\common\models;

use app\common\models\GoodsParam;

class Goods extends BaseModel
{
    public $table = 'yz_goods';
    public $display_order = 0;
    //protected $appends = ['status'];

    //public $fillable = ['display_order'];

    public $guarded = [];

    public static function getList($pagesize=20, $condition = [])
    {
        return static::uniacid()->paginate($pagesize);
    }

    public static function getGoodsById($id)
    {
        return static::find($id);
    }

    public function hasManyParams()
    {
        return $this->hasMany('app\common\models\GoodsParam');
    }

    public function hasManySpecs()
    {
        return $this->hasMany('app\common\models\GoodsSpec');
        //return $allspecs;

        /*foreach ($allspecs as &$s) {
            $s['items'] = pdo_fetchall("select a.id,a.specid,a.title,a.thumb,a.show,a.displayorder,a.valueId,a.virtual,b.title as title2 from " . tablename('sz_yi_goods_spec_item') . " a left join " . tablename('sz_yi_virtual_type') . " b on b.id=a.virtual  where a.specid=:specid order by a.displayorder asc",
                array(
                    ":specid" => $s['id']
                ));
        }
        unset($s);*/
    }


    /**
     * @param $keyword
     * @return mixed
     */
    public static function getGoodsByName($keyword)
    {
        return static::where('title', 'like', $keyword.'%')
            ->get()
            ->toArray();
    }
}
