<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:11
 */

class Slide extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_slide';

    protected $guarded = [''];

    protected $fillable = [''];

    public static function getSlidesIsEnabled()
    {
        return self::uniacid()
            ->where('enabled','1');
    }
    
    /**
     *  定义字段名
     * 可使
     * @return array */
    public  function atributeNames() {
        return [
            'slide_name'=> '幻灯片名称',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public  function rules() {
        return [
            'slide_name' => 'required',
        ];
    }
}
