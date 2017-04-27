<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:11
 */
class Address extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_address';

    protected $guarded = [''];

    protected $fillable = [''];


    public static function getProvince()
    {
        return self::where('level', '1')->get();
    }

}
