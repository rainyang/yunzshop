<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/27
 * Time: 17:54
 */

namespace app\platform\modules\system\models;


use app\common\models\BaseModel;
use app\common\helpers\Cache;

class SystemSetting extends BaseModel
{
    public $table = 'yz_system_setting';
    public $timestamps = true;
    protected $guarded = [''];

    /**
     * 保存数据
     * @param string $data
     * @param string $key
     * @param $cache_name
     * @return SystemSetting|bool
     */
    public static function settingSave($data = '', $key = '', $cache_name = '')
    {
        if (!$data && !$key) {
            return false;
        }

        $is_exists = self::where('key', $key)->first();
        $data = serialize($data);
        if (!$is_exists) {
            $system_setting = new self;
            // 添加
            $result = $system_setting::create([
                'key'       => $key,
                'value'     => $data
            ]);
        } else {
            // 修改
            $result = self::where('key', $key)->update(['value' => $data]);
        }
        $result ? Cache::put($cache_name, ['value' => $data] , 3600) : null;

        return $result;
    }

    /**
     * 读取数据
     * @param string $key
     * @param string $cache_name
     * @return SystemSetting
     */
    public static function settingLoad($key = '', $cache_name = '')
    {
         if (!Cache::has($cache_name)) {
            $result = self::getKeyList($key);
             Cache::put($cache_name, $result, 3600);
         } else {
             $result = Cache::get($cache_name);
         }

        if ($result) {
            $result = unserialize($result['value']);
        }

        return $result;
    }

    public function getKeyList($key)
    {
        return self::where('key', $key)->first();
    }
}