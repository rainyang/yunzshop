<?php

namespace app\common\components;

use app\common\traits\MessageTrait;
use app\common\traits\PermissionTrait;
use app\common\traits\TemplateTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Setting;
use Validator;
use Response;

/**
 * controller基类
 *
 * User: jan
 * Date: 21/02/2017
 * Time: 21:20
 */
class BaseController extends Controller
{
    use DispatchesJobs, MessageTrait, ValidatesRequests, TemplateTrait, PermissionTrait;


    public function __construct()
    {

    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    /**
     * 显示信息并跳转
     *
     * @param $message
     * @param string $redirect
     * @param string $status success  error danger warning  info
     * @return mixed
     */
    public function message($message, $redirect = '', $status = 'success')
    {
        return $this->render('web/message', [
            'redirect' => $redirect,
            'message' => $message,
            'status' => $status
        ]);
    }


    /**
     * 接口返回成功 JSON格式
     * @param string $message   提示信息
     * @param array $data       返回数据
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successJson($message = '', $data = [])
    {
        Response::json([
            'result' => 1,
            'msg' => $message,
            'data' => $data
        ])->send();
        return;
    }

    /**
     * 接口返回错误JSON 格式
     * @param string $message    提示信息
     * @param array $data        返回数据
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorJson($message = '', $data = [])
    {
         response()->json([
            'result' => 0,
            'msg' => $message,
            'data' => $data
        ])->send();
        return;
    }


}