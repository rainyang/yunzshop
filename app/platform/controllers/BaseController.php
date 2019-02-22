<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/20
 * Time: 上午10:53
 */

namespace app\platform\controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
     * 基础跳转公共方法
     * @param 1 $path 跳转路径
     * @param2 $message 响应提示
     * @param3  $isSuccess 是否是成功， 默认成功
     */
    protected function commonRedirect($path, $message = '', $isSuccess = 'success')
    {
        switch ($isSuccess){
            case 'success' :
                return redirect($path)->withSuccess($message .'成功！');
            case 'failed' :
                return redirect($path) ->withErrors($message.'失败！');
            case 'error' :
                return redirect($path)->withErrors("找不到该记录!");
            default :
                break;
        }
    }
}