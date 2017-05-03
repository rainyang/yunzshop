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
            $content = Curl::to('http://www.market.com/app-account/create')
                ->withData($data)
                ->get();
            $writeRes = Setting::set('shop.key', $requestModel);
            return $writeRes && $content;
        } else if($type == 'cancel') {
            $content = Curl::to('http://www.market.com/app-account/cancel')
                ->withData($data)
                ->get();
           // print_r($content);exit();
            $writeRes = Setting::set('shop.key', '');
            return $writeRes && $content ;
        }
    }

    /*
     * 检测是否有数据存在
     */
    public function isExist($data) {

        $type = \YunShop::request()->type;
        $domain = request()->getHttpHost();
        $content = Curl::to('http://www.market.com/update/check_isKey.json')
            ->withHeader(
               "Authorization: Basic " . base64_encode("{$data['key']}:{$data['secret']}")
            )
            ->withData([
                'type' => $type,
                'domain' => $domain
            ])
            ->get();

        if(strpos($content,'no such data exists') !== false) {
            $res = '密钥不存在';
        } else if(strpos($content,'expired of time') !== false){
            $res = '账号已经到期';
        } else if(strpos($content,'is ok') !== false) {
            $res = 'is ok';
        } else if(strpos($content, 'domain error') !== false) {
            $res = '域名不存在';
        }   else if(strpos($content, 'amount exceeded') !== false) {
            $res = '您的站点数量已经没有了，不能再建新站！若要建站请取消之前的站点，或者联系我们的客服人员！';
        }
        return $res;
    }

    //    /*
//     * write data into env
//     * @param $data : 代写入的数据
//     * @return bool
//     */
//    private function writeIntoENV($data, $type='create') {
//        $file = fopen(base_path('.env'), 'aw+');
//
//        foreach ($data as $key => $value)
//        {
//            try{
//                $keyType = strtoupper($key);
//                $originalValue = env($keyType);
//                if($type == 'create') {
//
//                    if(!$originalValue) {
//                        fwrite($file, "\r\n$keyType=$value\r\n");
//                    } else {
//                        $str = str_replace($originalValue, $value, file_get_contents(base_path('.env')));
//                        file_put_contents(base_path('.env'), $str);
//                    }
//
//                } else if($type == 'cancel') {
//                    if($originalValue){
//                        $str = str_replace("\r\n$keyType=$value\r\n", '', file_get_contents(base_path('.env')));
//                        file_put_contents(base_path('.env'), $str);
//                    }
//                }
//
//            }catch (\Exception $e) {
//                dd($e);
//                return false;
//            }
//
//        }
//        fclose($file);
//        return true;
//    }



}