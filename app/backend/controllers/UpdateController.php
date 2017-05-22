<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 11:13
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\facades\Option;
use app\common\facades\Setting;
use app\common\services\AutoUpdate;

class UpdateController extends BaseController
{

    public function index()
    {
        $list = [];

        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        $update->setBasicAuth($key, $secret);

        if ($update->checkUpdate() === false) {
            $this->error('检测更新列表失败');
        }

        if ($update->newVersionAvailable()) {
            $list = $update->getUpdates();
        }
        krsort($list);
        return view('update.index', ['list' => $list])->render();
    }


    /**
     * 检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $result = ['msg' => '', 'last_version' => '', 'updated' => 0];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if(!$key || !$secret) {
            $result['msg'] = 'key or secret is null';
             response()->json($result)->send();
            return;
        }

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        $update->setBasicAuth($key, $secret);
        //$update->setBasicAuth();

        //Check for a new update
        if ($update->checkUpdate() === false) {
            $result['msg'] = 'Could not check for updates! See log file for details.';
              response()->json($result)->send();
            return;
        }

        if ($update->newVersionAvailable()) {
            $result['last_version'] = $update->getLatestVersion()->getVersion();
            $result['updated'] = 1;
            $result['current_version'] = config('version');
        }
         response()->json($result)->send();
        return;
    }


    /**
     * 开始下载并更新程序
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startDownload()
    {
        $resultArr = ['msg'=>'','status'=>0,'data'=>[]];
        set_time_limit(0);

        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        Setting::get('auth.key');
        $update->setBasicAuth($key, $secret);
        //Check for a new update
        if ($update->checkUpdate() === false) {
            $resultArr['msg'] = 'Could not check for updates! See log file for details.';
             response()->json($resultArr)->send();
            return;
        }

        if ($update->newVersionAvailable()) {
            $update->onEachUpdateFinish(function($version){
                \Artisan::call('update:version' ,['version'=>$version]);
            });
            $result = $update->update();

            if ($result === true) {
                $resultArr['status'] = 1;
                $resultArr['msg'] = 'Update simulation successful';
            } else {
                $resultArr['msg'] = 'Update simulation failed: ' . $result;
                if ($result = AutoUpdate::ERROR_SIMULATE) {
                    $resultArr['data'] = $update->getSimulationResults();
                }
            }
        } else {
            $resultArr['msg'] = 'Current Version is up to date';
        }
         response()->json($resultArr)->send();
        return;
    }

}