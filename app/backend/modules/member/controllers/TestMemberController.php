<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 13:52
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\TestMember;
use app\common\components\BaseController;
use app\common\events\TestFailEvent;
use app\common\events\UserActionEvent;
use app\common\helpers\ImageHelper;
use app\common\helpers\PaginationHelper;
use app\common\services\WechatPay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


class TestMemberController extends BaseController
{


    public function testLogin()
    {
        //表单数据交验
        $validator = (new TestMember())->validator(Input::get());
        print_r($validator->messages());

        $this->render('test', ['a' => '123456']);
    }

    public function testMessage()
    {
        $validator = (new TestMember())->validator(['title'=>'','body'=>'1']);

        //flash('这里将跳转','danger');
        //flash($validator->messages(),'danger');
       // return redirect(Url::absoluteWeb('member.test-member.test-login'));
        //flash()->overlay('Notice', Url::absoluteWeb('member.test-member.test-login'));
        $this->error($validator->messages())->important();
        //$this->overlay($validator->messages(), 'Modal Title');
        $this->render('test-message', ['a' => '123456']);

    }


    public function testStore(Request $request)
    {
        //判断是否ajax
        if ($request->ajax()) {

        }

        //数据检验
        $messages = [
            'required' => ' :attribute不能为空!',
            'min' => ' :attribute不能少于:min!',
        ];

        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required|min:3',
        ],$messages);

        //自定义字段名
        $niceNames =['title' => '标题',];
        $validator->setAttributeNames($niceNames);


        //获取单个错误信息
        print_r($validator->messages()->get('title'));
        echo "<br/>";
        //获取全部错误信息
        print_r($validator->messages());
        echo "<br/>";

        //输出json
        echo new JsonResponse($validator->messages());
        echo response()->json($validator->messages());

        //判断结果
        if ($validator->fails()) {
            echo "<br/>:fails<br/>";
            echo "<br/>";
            //触发事件
            \Event::fire(new TestFailEvent($validator->messages()));

            //返回信息
            return redirect('http://baidu.com')
                ->withErrors($validator)
                ->withInput();
        }else{
            //触发事件
            event(new UserActionEvent('app\backend\modules\member\models\TestMember', 22, 1, '添加了会员'));
        }
    }

    public function testFactory()
    {
        $members = factory(TestMember::class,3)->create();
        print_r($members);
    }

    public function testUpload()
    {
        echo ImageHelper::tplFormFieldImage('image');
    }

    public function testPage()
    {
        echo PaginationHelper::show(18,1);
    }

    public function pay()
    {
        $pay = WechatPay();
        $result = $pay->doRefund('SN20170417200901044483', '0.01', '0.01');

        dd($result);
    }



}