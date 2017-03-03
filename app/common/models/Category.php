<?php

namespace app\common\models;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Category extends BaseModel
{
    public $table = 'yz_category';
    public $display_order = '0';
    public $thumb = '';
    public $description = '';
    public $adv_img = '';
    public $adv_url = '';

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    protected $fillable = [''];

    /**
     * @param $parent_id
     * @param $pageSize
     * @return mixed
     */
    public static function getCategorys($parent_id, $pageSize)
    {
        $data = self::uniacid()
            ->where('parent_id', $parent_id)
            ->orderBy('id', 'asc')
            ->paginate($pageSize)
            ->toArray();
        return $data;
    }

    /**
     * @param $uniacid
     * @param $parent_id
     * @return mixed
     */
    public static function getCategoryTotal($uniacid,  $parent_id)
    {
        return self::where('uniacid', $uniacid)
            ->where('parent_id', $parent_id)
            ->count();
    }

}