<?php
namespace  app\backend\controllers;

use app\api\controller\favorite\Set;
use app\common\components\BaseController;
use Setting;
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 16:46
 */
class TestController extends BaseController
{
    public function index()
    {
        echo __CLASS__;
    }

    public function testSetting()
    {
        Setting::$uniqueAccountId = 1;
        $value = Setting::set('config.test','default value');
        $value = Setting::set('config.test.t','default value t');
        $value = Setting::set('config.test.f','default value f');
        dd($value);

        $setting = new Setting();
        $uniqueAccountId = 1;
        //测试字符
        $setting->setValue($uniqueAccountId,'config.test','test value');
        echo $setting->getValue($uniqueAccountId,'config.test','default value');
        echo "<br/>";
        $setting->setValue($uniqueAccountId,'config.test.test2','test2 value');
        echo $setting->getValue($uniqueAccountId,'config.test.test2','default2 value');
        echo "<br/>";
        $setting->setValue($uniqueAccountId,'config.test.test3','test value3');
        echo $setting->getValue($uniqueAccountId,'config.test.test3','default value3');
        echo "<br/>";
        //测试数组
        $setting->setValue($uniqueAccountId,'config.test2.array',['test-key'=>'ddd']);
        $arr = $setting->getValue($uniqueAccountId,'config.test2.array',[]);
        print_r($arr);
        echo "<br/>";
        //测试数组
        $setting->setValue($uniqueAccountId,'config.test2.bb',['testbb-key'=>'dbbbbdd']);
        $arr = $setting->getValue($uniqueAccountId,'config.test2.bb',[]);
        print_r($arr);
        echo "<br/>";
        $setting->setValue($uniqueAccountId,'test1','test value1');
        echo $setting->getValue($uniqueAccountId,'test1','default value1');
        echo "<br/>";
        //测试数组
        $setting->setValue($uniqueAccountId,'test1',['testbb-key'=>'test1']);
        $arr = $setting->getValue($uniqueAccountId,'test1',['a1']);
        print_r($arr);
        echo "<br/>";
        //测试数组
        $setting->setValue($uniqueAccountId,'test2',['testbb-key'=>'test2']);
        $arr = $setting->getValue($uniqueAccountId,'test2',['a']);
        print_r($arr);
        echo "<br/>";
        $configs = Setting::fetchSettings($uniqueAccountId,'config')->toArray();
        print_r($configs);
        echo "<br/>";
        $configs = $setting->getItems($uniqueAccountId,'config');
        print_r($configs);
        echo "<br/>";

    }

    public function testOld()
    {
        $hm = Setting::get('shop.app');
        print_r($hm);
    }


}