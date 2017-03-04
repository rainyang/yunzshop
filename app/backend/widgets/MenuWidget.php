<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets;


use app\common\components\Widget;

class MenuWidget extends Widget
{
    public $test = '';

    public function run()
    {

        return $this->render('widgets/menu',['test'=>$this->test]);
    }
}