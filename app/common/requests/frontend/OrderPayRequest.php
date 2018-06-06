<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:53
 */

namespace app\common\requests\frontend;

use Illuminate\Foundation\Http\FormRequest;

class OrderPayRequest extends FormRequest
{
    public function rules(){
        return [
            'order_ids'=>'required|JSON',
        ];
    }

}