<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 13:52
 */

namespace app\backend\modules\member\controllers;


use app\common\components\BaseController;
use app\common\events\TestFailEvent;
use app\common\events\UserActionEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;


class TestMemberController extends BaseController
{


    public function testLogin()
    {
        $this->render('test', ['a' => '123456']);
    }


    public function testStore(Request $request)
    {
        //判断是否ajax
        if ($request->ajax()) {

        }

        //数据检验
        $messages = [
            'required' => 'The {field} has to be 6 chars long!'
        ];
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
        ],$messages);
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
}