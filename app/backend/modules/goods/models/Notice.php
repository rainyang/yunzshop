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

    public static function relationSave($goodsId, $data, $operate)
    {
        if(!$goodsId){
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        }

        $notices_data = [
            'goods_id' => $goodsId,
            'uid' => $data['uid']
        ];
        $request = false;
        foreach ($data['type'] as $type) {
            $notices_data['type'] = $type;
            $saleModel->setRawAttributes($notices_data);
            $request = $saleModel->save();
        }
        return $request;
    }

    public static function getModel($goodsId,$operate)
    {
        $model = false;
        if($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model =  new static;

        return $model;
    }
}