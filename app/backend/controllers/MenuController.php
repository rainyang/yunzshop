<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 09/03/2017
 * Time: 11:00
 */

namespace app\backend\controllers;


use app\backend\models\Menu;
use app\common\components\BaseController;
use app\common\facades\Option;

class MenuController extends BaseController
{
    public function index()
    {

        return $this->render('index',[]);
    }

    public function add()
    {
        $model = new Menu();

        $form = Option::form('unique_id', '配置标题', function($form) {
            $form->text('example_plugin_text', '文本框')
                ->hint('通过 hint 方法可以在这里添加提示');

            $form->checkbox('example_plugin_checkbox', '这是复选框')->label('这是复选框的标签');

            $form->checkbox('example_plugin_checkbox2', '禁用元素')->label('按 E 可赛艇')->disabled();

            $form->select('example_plugin_select', '下拉选择框')
                ->option('0', '蛤蛤蛤')
                ->option('1', '戳木娘')
                ->option('2', '你好啊')
                ->description('通过 <code>description</code> 方法可以在这里添加描述');

            $form->group('example_plugin_group', '这是 Input Group')
                ->text('example_plugin_group')->addon('个智障');

            $form->textarea('example_plugin_textarea', '这是 Textarea')->rows(10)->value('
          目前 OptionForm 支持这些 input 元素：["text", "checkbox", "textarea", "select", "group"]
          $form->{input}() 得到的 OptionItem 实例支持链式调用
          ==============================
          你可以使用 value 方法手动设定各个 OptionItem 的值
          如果没有设置，OptionForm 将会从 options 表中通过 id 寻找并自动赋值
          可以通过 with 方法手动绑定数据到表单上，自动赋值时会优先从这里寻找
          id 为 xxx[yy] 格式的 option 将会被自动转化为数组并序列化存入数据库
          ==============================
          定义完 OptionForm 后不要忘记执行 handle 方法，否则不会响应 POST 请求
          这个方法在哪里执行都可以，在一处定义，另一处执行也没关系
          ==============================
          OptionForm 有许多定制方法：

          type：修改表单外 box 的边框颜色，支持 ["default", "primary", "success", "info", "warning", "danger"]
          addButton：添加按钮到表单底部
          addMessage：添加信息提示到表单顶部
          always：设置一个表单渲染之前总会被执行的回调
          renderWithOutTable：不要用 table 元素包裹 OptionItem
          renderInputTagsOnly：只渲染 input 等元素，不渲染左边的标题
          renderWithOutSubmitButton：不渲染「提交」按钮

          注意：定制完毕后都要使用 render 方法渲染表单

        ')->disabled();
        })->addMessage('更多示例请查看 稍后给出，蟹蟹')->handle();

        return $this->render('form',compact('model','form'));
    }

    public function edit()
    {
        return $this->render('form',[]);
    }

    public function delete()
    {

    }
}