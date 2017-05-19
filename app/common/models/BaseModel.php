<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/02/2017
 * Time: 16:36
 */

namespace app\common\models;


use app\common\exceptions\AdminException;
use app\common\exceptions\ShopException;
use app\common\traits\ValidatorTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use ValidatorTrait;
    protected $search_fields;

    /**
     * 模糊查找
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeSearchLike($query, $params)
    {
        $search_fields = $this->search_fields;
        $query->where(function ($query) use ($params, $search_fields) {
            foreach ($search_fields as $search_field) {
                $query->orWhere($search_field, 'like', '%' . $params . '%');
            }
        });
        return $query;
    }

    /**
     * 默认使用时间戳戳功能
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value)
    {
        return $value;
    }

    /**
     * select的时候避免转换时间为Carbon
     *
     * @param mixed $value
     * @return mixed
     */
//  protected function asDateTime($value) {
//	  return $value;
//  }


    /**
     * 从数据库获取的为获取时间戳格式
     *
     * @return string
     */
    //public function getDateFormat() {
    //     return 'U';
    // }

    //后台全局筛选统一账号scope
    public function scopeUniacid($query)
    {
        return $query->where('uniacid', \YunShop::app()->uniacid);
    }

    /**
     * 递归获取$class 相对路径的 $findClass
     * @param $class
     * @param $findClass
     * @return null|string
     */
    public static function recursiveFindClass($class, $findClass)
    {
        $result = substr($class, 0, strrpos($class, "\\")) . '\\' . $findClass;

        if (class_exists($result)) {
            return $result;
        }

        if(class_exists(get_parent_class($class))){
            return self::recursiveFindClass(get_parent_class($class),$findClass);
        }
        return null;

    }

    /**
     * 获取与子类 继承关系最近的 $model类
     * @param $model
     * @return null|string
     * @throws ShopException
     */
    public function getNearestModel($model)
    {
        $result = self::recursiveFindClass(static::class,$model);

        if(isset($result)){
            return $result;
        }
        throw new ShopException('获取关联模型失败');
    }
}