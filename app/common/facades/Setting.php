<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 01/03/2017
 * Time: 12:51
 */

namespace app\common\facades;


use Illuminate\Support\Facades\Facade;
use app\common\models\Setting as SettingModel;

class Setting extends Facade
{

    private static $instance;

    public function __construct()
    {

    }

    protected static function getFacadeAccessor()
    {
        return 'setting';
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SettingModel();
        }
        return self::$instance;
    }


    /**
     * 设置配置信息
     *
     * @param $key
     * @param null $value
     */
    public static function set($key, $value = null)
    {
        return self::getInstance()->setValue( $key, $value);
    }

    /**
     * 获取配置信息
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return self::getInstance()->getValue( $key, $default);
    }

    /**
     * 检测是否存在分组
     * @param $group
     * @return bool
     */
    public static function exitsGroup($group)
    {
        return self::getInstance()->exists( $group);
    }

    /**
     * 获取分组所有值
     * @param $group
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllByGroup($group)
    {
        return self::getInstance()->fetchSettings($group);
    }
}