<?php
namespace app\backend\modules\goods\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:18
 */

class Notice extends \app\common\models\Notice
{
    public $timestamps = false;

    /**
     * @param $goodsId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getInfo($goodsId)
    {
        return self::where('goods_id', $goodsId)
        ->first();
    }

    /**
     * @param $notices
     * @return mixed
     */
    public static function createdNotices($notices)
    {
        return self::insert($notices);
    }

    /**
     * @param $goodsId
     * @param $notices
     * @return mixed
     */
    public static function updatedNotices($goodsId, $notices)
    {
        return self::where('goods_id', $goodsId)->update($notices);
    }

    /**
     * @param $goodsId
     * @return mixed
     */
    public static function deletedNotices($goodsId)
    {
        return self::where('goods_id', $goodsId)->delete();
    }
    
    public static function getList($goodsId)
    {
        return self::where('goods_id',$goodsId)
            ->get();
    }

    public static function relationSave($goodsId, $data, $operate = '')
    {
        if(!$goodsId){
            return false;
        }

        self::deleteAllByGoodsId($goodsId);

        $noticesData = [
            'goods_id' => $goodsId,
            'uid' => $data['uid']
        ];
        return self::addByGoodsId($data, $noticesData);

    }

    public static function deleteAllByGoodsId($goodsId)
    {
        return static::where(['goods_id' => $goodsId])->delete();
    }

    public static function addByGoodsId($data,$noticesData)
    {
        foreach ($data['type'] as $type) {
            $saleModel = new static;
            $noticesData['type'] = $type;
            $saleModel->setRawAttributes($noticesData);
             $saleModel->save();
        }

        return true;
    }


}