<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\modules\setting\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;
use app\common\services\AutoUpdate;
use app\common\services\MyLink;
use Ixudra\Curl\Facades\Curl;

class KeyController extends BaseController
{

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid;
        $this->_log = app('log');
    }

    /**
     * 密钥填写
     * @return mixed
     */
    public function index()
    {
        $requestModel = \YunShop::request()->upgrade;
        $upgrade = Setting::get('shop.key');
        $type = \YunShop::request()->type;
        $message = $type == 'create' ? '添加' : '取消';
        if ($requestModel) {
            //检测数据是否存在
            $res = $this ->isExist($requestModel);
            //var_dump($res);exit();
            if(!$res['isExists']) {

                if($res['message'] == 'amount exceeded')
                    $this->error('您已经没有剩余站点数量了，如添加新站点，请取消之前的站点或者联系我们的客服人员！');
                else
                    $this->error('Key或者密钥出错了！');

            } else {
                if ($this->processingKey($requestModel, $type)) {
                    return $this->message("站点{$message}成功", Url::absoluteWeb('setting.key.index'));
                } else {
                    $this->error("站点{$message}失败");
                }
            }
        }
        return view('setting.key.index', [
            'set' => $upgrade,
        ])->render();
    }

      /*
     * 处理信息
     */
    private function processingKey($requestModel, $type)
    {
        $domain = request()->getHttpHost();
        $data = [
            'uniacid' =>$this->uniacid,
            'key' => $requestModel['key'],
            'secret' => $requestModel['secret'],
            'domain' => $domain
        ];
        if($type == 'create') {

            $content = Curl::to(config('auto-update.checkUrl').'/app-account/create')
                ->withData($data)
                ->get();
           // dd($content);exit();
            $writeRes = Setting::set('shop.key', $requestModel);

            \Cache::forget('app_auth' . $this->uniacid);

            return $writeRes && $content;

        } else if($type == 'cancel') {

            $content = Curl::to(config('auto-update.checkUrl').'/app-account/cancel')
                ->withData($data)
                ->get();
            //var_dump($content);exit();

            $writeRes = Setting::set('shop.key', '');

            \Cache::forget('app_auth' . $this->uniacid);

            return $writeRes && $content ;
        }
    }

    /*
     * 检测是否有数据存在
     */
    public function isExist($data) {

        $type = \YunShop::request()->type;
        $domain = request()->getHttpHost();

        $filename = config('auto-update.checkUrl').'/check_isKey.json';
        $postData = [
            'type' => $type,
            'domain' => $domain
        ];
        $update = new AutoUpdate();
        $res = $update -> isKeySecretExists($filename, $data, $postData, 'auto_update ' . $this->uniacid . ' ');
        return $res;
    }





}