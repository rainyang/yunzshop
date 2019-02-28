<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/27
 * Time: 17:54
 */
namespace app\platform\modules\system\models;

use Illuminate\Database\Eloquent\Model;
use app\common\models\BaseModel;

class SystemSetting extends BaseModel
{
    public $table = 'yz_system_setting';
    public $timestamps = true;
    protected $guarded = [''];

    public static function setHotelSetting($hotel_id)
    {
        $setting_data = self::getDefaultSetting();
        foreach ($setting_data as $key => $value) {
            self::create([
                'hotel_id'  => $hotel_id,
                'key'       => $key,
                'value'     => $value
            ]);
        }
    }
}