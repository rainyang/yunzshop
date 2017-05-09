<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\modules\setting\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;
use app\common\models\AccountWechats;
use app\common\services\MyLink;
use Illuminate\Support\Facades\Request;
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
            $this->_log->error($this->uniacid . " : " . $requestModel['key'] . ' => ' . $requestModel['secret']);
            $res = $this ->isExist($requestModel);
            $this->_log->error('1 isExists result ===> ' . $res);
            if($res !== 'is ok') {
                $this ->error($res);
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
            $this->_log->error('app-account create === '. $data['uniacid'] . " :: " . $data['key'] . " :: " . $data['secret'] . " :: " . $data['domain'] .$content);
            $writeRes = Setting::set('shop.key', $requestModel);
            Cache::forget('app_auth' . $this->uniacid);
            $this->_log->error('shop,key set ' . $writeRes . ': ' . $requestModel['key'] . '=> ' . $requestModel['secret']);
            return $writeRes && $content;
        } else if($type == 'cancel') {
            $content = Curl::to(config('auto-update.checkUrl').'/app-account/cancel')
                ->withData($data)
                ->get();
            $this->_log->error('app-account cancel' . $content);
            $writeRes = Setting::set('shop.key', '');
            Cache::forget('app_auth' . $this->uniacid);
            $this->_log->error('shop,key cancel ' . $writeRes . ': ' . $requestModel['key'] . '=> ' . $requestModel['secret']);
            return $writeRes && $content ;
        }
    }

    /*
     * 检测是否有数据存在
     */
    public function isExist($data) {

        $type = \YunShop::request()->type;
        $domain = request()->getHttpHost();
        $content = Curl::to(config('auto-update.checkUrl').'/check_isKey.json')
            ->withHeader(
               "Authorization: Basic " . base64_encode("{$data['key']}:{$data['secret']}")
            )
            ->withData([
                'type' => $type,
                'domain' => $domain
            ])
            ->get();

        $res = '密钥或者Key 有错误！';
        if(strpos($content,'no such data exists') !== false) {
            $this->_log->error('uniacid ==' .$this->uniacid. ' no such data exists');
        } else if(strpos($content,'expired of time') !== false){
            $this->_log->error('uniacid ==' .$this->uniacid. 'expired of time');
        } else if(strpos($content,'is ok') !== false) {
            $res = 'is ok';
        } else if(strpos($content, 'domain error') !== false) {
            $this->_log->error('uniacid ==' .$this->uniacid. "domain doesn't exists!");
        }   else if(strpos($content, 'amount exceeded') !== false) {
            $res = '您的站点数量已经没有了，不能再建新站！若要建站请取消之前的站点，或者联系我们的客服人员！';
        }
        return $res;
    }





}