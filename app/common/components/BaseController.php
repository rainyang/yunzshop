<?php

namespace app\common\components;

use app\common\traits\JsonTrait;
use app\common\traits\MessageTrait;
use app\common\traits\PermissionTrait;
use app\common\traits\TemplateTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Validator;

/**
 * controller基类
 *
 * User: jan
 * Date: 21/02/2017
 * Time: 21:20
 */
class BaseController extends Controller
{
    use DispatchesJobs, MessageTrait, ValidatesRequests, TemplateTrait, PermissionTrait,JsonTrait;


    public function __construct()
    {
        $this->setCookie();
    }

    /**
     * 前置action
     */
    public function preAction()
    {

    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }



    /**
     * 后台url参数验证
     * @param \Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     */
    public function validate(\Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            echo $this->message($validator->errors()->first());exit;
        }
    }

    /**
     * 设置Cookie存储
     *
     * @return void
     */
    private function setCookie()
    {
        $session_id = '';
        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
            $pieces = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (empty($session_id) && \YunShop::request()->session_id &&
            \YunShop::request()->session_id != 'undefined') {
            $session_id = \YunShop::request()->session_id;
        }

        if (!empty($session_id)) {
            session_id($session_id);
        }

        session_start();
    }




}