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
use Illuminate\Filesystem\Filesystem;

class UpdateController extends BaseController
{

    public function index()
    {
        /*$list = [];

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
        $version = config('version');
        return view('update.upgrad', [
            'list' => $list,
            'version' => $version,
        ])->render();*/

        return view('update.upgrad')->render();
    }

    /**
     * footer检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $result = ['msg' => '', 'last_version' => '', 'updated' => 0];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if(!$key || !$secret) {
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
     * 检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyheck()
    {
        $filesystem = new Filesystem();

        $result = ['msg' => '', 'last_version' => '', 'updated' => 0];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if(!$key || !$secret) {
            return;
        }

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('backcheck_app.json');
        $update->setCurrentVersion(config('version'));

        if (config('app.debug')) {
            $update->setUpdateUrl('http://yun-yzshop.com/update'); //Replace with your server update directory
        } else {
            $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        }

        $update->setBasicAuth($key, $secret);
        //$update->setBasicAuth();

        //Check for a new update
        $ret = $update->checkBackUpdate();

        if (is_array($ret)) {
            if (1 == $ret['result']) {
                $files = [];

                if (!empty($ret['files'])) {
                    foreach ($ret['files'] as $file) {
                        $entry = base_path() . '/' . $file['path'];
                        //如果本地没有此文件或者文件与服务器不一致
                        if (!is_file($entry) || md5_file($entry) != $file['md5']) {
                            $files[] = array(
                                'path' => $file['path'],
                                'download' => 0
                            );
                            $difffile[] = $file['path'];
                        } else {
                            $samefile[] = $file['path'];
                        }
                    }
                }

                $tmpdir = storage_path('app/public/tmp/'. date('ymd'));
                if (!is_dir($tmpdir)) {
                    $filesystem->makeDirectory($tmpdir, '0777', true);
                }

                $ret['files'] = $files;
                file_put_contents($tmpdir . "/file.txt", json_encode($ret));

                $result = [
                    'result' => 1,
                    'version' => $ret['version'],
                    'files' => $ret['files'],
                    'filecount' => count($files),
                    //'log' => str_replace("\r\n", "<br/>", base64_decode($ret['log']))
                ];
            }
        }

        return response()->json($result)->send();
    }

    public function fileDownload()
    {
        $filesystem = new Filesystem();

        $tmpdir  = storage_path('app/public/tmp/'. date('ymd'));
        $f       = file_get_contents($tmpdir . "/file.txt");
        $upgrade = json_decode($f, true);
        $files   = $upgrade['files'];
        $path    = "";
        $nofiles = \YunShop::request()->nofiles;

        //找到一个没更新过的文件去更新
        foreach ($files as $f) {
            if (empty($f['download'])) {
                $path = $f['path'];
                break;
            }
        }

        if (!empty($path)) {
            if (!empty($nofiles)) {
                if (in_array($path, $nofiles)) {
                    foreach ($files as &$f) {
                        if ($f['path'] == $path) {
                            $f['download'] = 1;
                            break;
                        }
                    }
                    unset($f);
                    $upgrade['files'] = $files;
                    $tmpdir           = storage_path('app/public/tmp/'. date('ymd'));
                    if (!is_dir($tmpdir)) {
                        $filesystem->makeDirectory($tmpdir, '0777', true);
                    }
                    file_put_contents($tmpdir . "/file.txt", json_encode($upgrade));

                    return response()->json(['result' => 3])->send();
                }
            }

            $key = Setting::get('shop.key')['key'];
            $secret = Setting::get('shop.key')['secret'];
            if(!$key || !$secret) {
                return;
            }

            $update = new AutoUpdate(null, null, 300);
            $update->setUpdateFile('backdownload_app.json');
            $update->setCurrentVersion(config('version'));

            if (config('app.debug')) {
                $update->setUpdateUrl('http://yun-yzshop.com/update'); //Replace with your server update directory
            } else {
                $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
            }

            $update->setBasicAuth($key, $secret);

            //Check for a new download
            $ret = $update->checkBackDownload([
                'path' => $path
            ]);
echo '<pre>';print_r($ret);exit;
            if (is_array($ret)) {
                $path    = $ret['path'];
                $dirpath = dirname($path);

                if (!is_dir(base_path($dirpath))) {
                    $filesystem->makeDirectory($dirpath, '0777', true);
                }

                $content = base64_decode($ret['content']);
                file_put_contents(base_path($path), $content);

                $success = 0;
                foreach ($files as &$f) {
                    if ($f['path'] == $path) {
                        $f['download'] = 1;
                        break;
                    }
                    if ($f['download']) {
                        $success++;
                    }
                }
                unset($f);
                $upgrade['files'] = $files;
                $tmpdir           = storage_path('app/public/tmp/'. date('ymd'));

                if (!is_dir($tmpdir)) {
                    $filesystem->makeDirectory($tmpdir, '0777', true);
                }
                file_put_contents($tmpdir . "/file.txt", json_encode($upgrade));

                return response()->json([
                    'result' => 1,
                    'total' => count($files),
                    'success' => $success
                ])->send();
            }
        }
    }

    /**
     * 开始下载并更新程序
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startDownload()
    {
        \Cache::flush();
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
                \Log::debug('----CLI----');
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