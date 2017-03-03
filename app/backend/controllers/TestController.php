<?php
namespace  app\backend\controllers;

use app\backend\modules\member\models\TestMember;
use app\common\components\BaseController;
use Illuminate\Support\Str;
use Setting;
use app\common\services\PluginManager;
use Datatables;
use Cookie;
use iscms\Alisms\SendsmsPusher as Sms;

class TestController extends BaseController
{
    public function __construct(Sms $sms)
    {
        $this->sms=$sms;
    }
    public function index()
    {

        return $this->render('index');

    }

    public function test()
    {
        return widget('app\backend\widgets\MenuWidget',['test'=>'bbbbb']);
    }

    public function view()
    {
        return view('test.index',['a'=>Str::random(10)]);
    }

    public function testSms()
    {
        $result=$this->sms->send("phone","name","content","code");
    }


    public function testPlugin()
    {
        //Illuminate\Session\Store;
        session()->put('test','jan');//设置session
        echo session('test','default');//获取session 后面的参数为默认值
        session()->forget('test');//注销session
        echo session('test','default');
        echo "<br />";
        //Illuminate\Contracts\Cookie;
        //设置cookie
         Cookie::queue('test', 'can you read me?', 99999999);
        echo  Cookie::queued('test','b');
        //注销cookie
        Cookie::unqueue('test');
        echo  Cookie::queued('test','a');
    }

    public function pluginData(PluginManager $plugins)
    {
        $installed = $plugins->getPlugins();

        return Datatables::of($installed)
            ->setRowId('plugin-{{ $name }}')
            ->editColumn('title', function ($plugin) {
                return trans($plugin->title);
            })
            ->editColumn('description', function ($plugin) {
                return trans($plugin->description);
            })
            ->editColumn('author', function ($plugin) {
                return "<a href='{$plugin->url}' target='_blank'>".trans($plugin->author)."</a>";
            })
            ->addColumn('status', function ($plugin) {
                return trans('admin.plugins.status.'.($plugin->isEnabled() ? 'enabled' : 'disabled'));
            })
            ->addColumn('operations', function ($plugin) {
                return view('vendor.admin-operations.plugins.operations', compact('plugin'));
            })
            ->make(true);
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