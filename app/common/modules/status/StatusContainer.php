<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:20
 */

namespace app\common\modules\status;

use Illuminate\Container\Container;

class StatusContainer extends Container
{
    /**
     * StatusContainer constructor.
     */
    public function __construct()
    {
        return app()->singleton('StatusContainer',function(){
            return new static();
        });
    }
}