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
        $list = [];

        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('front-version'));

        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        $update->setBasicAuth($key, $secret);

        $update->checkUpdate();

        if ($update->newVersionAvailable()) {
            $list = $update->getUpdates();
        }

        krsort($list);
        $version = config('front-version');

        return view('update.upgrad', [
            'list' => $list,
            'version' => $version,
            'count' => count($list)
        ])->render();
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

        $res = $update->checkUpdate();

        //Check for a new update
        if ($res === false) {
            $result['msg'] = 'Could not check for updates! See log file for details.';
            response()->json($result)->send();
            return;
        }

        if (isset($res['result']) && 0 == $res['result']) {
            $res['updated'] = 0;
            return response()->json($res)->send();
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
        set_time_limit(0);

        $filesystem = app(Filesystem::class);
        $update = new AutoUpdate(null, null, 300);

        $filter_file = ['composer.json', 'composer.lock', 'README.md', 'config/front-version'];
        $plugins_dir = $update->getDirsByPath('plugins', $filesystem);

        $result = ['result' => 0, 'msg' => '网络请求超时', 'last_version' => ''];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if(!$key || !$secret) {
            return;
        }

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('backcheck_app.json');
        $update->setCurrentVersion(config('version'));

        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        $update->setBasicAuth($key, $secret);
        //$update->setBasicAuth();

        //Check for a new update
        $ret = $update->checkBackUpdate();

        if (is_array($ret)) {
            if (1 == $ret['result']) {
                $files = [];

                if (!empty($ret['files'])) {
                    foreach ($ret['files'] as $file) {
                        if (in_array($file['path'], $filter_file)) {
                            continue;
                        }

                        //忽略前端样式文件
                        if (preg_match('/^static\/app/', $file['path'])) {
                            continue;
                        }

                        //忽略没有安装的插件
                        if (preg_match('/^plugins/', $file['path'])) {
                            $sub_dir = substr($file['path'], strpos($file['path'], '/')+1);
                            $sub_dir = substr($sub_dir, 0, strpos($sub_dir, '/'));

                            if (!in_array($sub_dir, $plugins_dir)) {
                                continue;
                            }
                        }

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
                    $filesystem->makeDirectory($tmpdir, '0755', true);
                }

                $ret['files'] = $files;
                file_put_contents($tmpdir . "/file.txt", json_encode($ret));

                if (empty($files)) {
                    $version = config('version');
                    //TODO 更新日志记录
                } else {
                    $version = $ret['version'];
                }

                $result = [
                    'result' => 1,
                    'version' => $version,
                    'files' => $ret['files'],
                    'filecount' => count($files),
                    'log' => nl2br(base64_decode($ret['log']))
                ];
            } else {
                preg_match('/"[\d\.]+"/', file_get_contents(base_path('config/') . 'version.php'), $match);
                $version = $match ? trim($match[0], '"') : '1.0.0';

                $result = ['result' => 99, 'msg' => '', 'last_version' => $version];
            }
        }

        response()->json($result)->send();
    }

    public function fileDownload()
    {
        $filesystem = app(Filesystem::class);

        $tmpdir  = storage_path('app/public/tmp/'. date('ymd'));
        $f       = file_get_contents($tmpdir . "/file.txt");
        $upgrade = json_decode($f, true);
        $files   = $upgrade['files'];
        $total   = count($upgrade['files']);
        $path    = "";
        $nofiles = \YunShop::request()->nofiles;
        $status  = 1;

        $update = new AutoUpdate(null, null, 300);

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
                        $filesystem->makeDirectory($tmpdir, '0755', true);
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

            $update->setUpdateFile('backdownload_app.json');
            $update->setCurrentVersion(config('version'));

            $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

            $update->setBasicAuth($key, $secret);

            //Check for a new download
            $ret = $update->checkBackDownload([
                'path' => urlencode($path)
            ]);

            //预下载
            if (is_array($ret)) {
                $path    = $ret['path'];
                $dirpath = dirname($path);
                $save_path = storage_path('app/auto-update/shop') . '/' . $dirpath;

                if (!is_dir($save_path)) {
                    $filesystem->makeDirectory($save_path, '0755', true);
                }

                //新建
                $content = base64_decode($ret['content']);
                file_put_contents(storage_path('app/auto-update/shop') . '/' . $path, $content);

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
                    $filesystem->makeDirectory($tmpdir, '0755', true);
                }

                file_put_contents($tmpdir . "/file.txt", json_encode($upgrade));
            }
        } else {
            //覆盖
            foreach ($files as $f) {
                $path = $f['path'];
                $file_dir = dirname($path);

                if (!is_dir(base_path($file_dir))) {
                    $filesystem->makeDirectory(base_path($file_dir), '0755', true);
                }

                $content = file_get_contents(storage_path('app/auto-update/shop') . '/' . $path);

                if (!empty($content)) {
                    file_put_contents(base_path($path), $content);

                    @unlink(storage_path('app/auto-update/shop') . '/' . $path);
                }
            }

            //$filesystem->deleteDirectory(storage_path('app/auto-update/shop'));

            //更新完执行数据表
            \Log::debug('----CLI----');
            $plugins_dir = $update->getDirsByPath('plugins', $filesystem);
            \Artisan::call('update:version' ,['version'=>$plugins_dir]);

            $status = 2;

            $success = $total;
        }

        response()->json([
            'result' => $status,
            'total' => $total,
            'success' => $success
        ])->send();
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
            /*$update->onEachUpdateFinish(function($version){
                \Log::debug('----CLI----');
                \Artisan::call('update:version' ,['version'=>$version]);
            });*/
            $result = $update->update();

            if ($result === true) {
                $list = $update->getUpdates();
                if (!empty($list)) {
                    $this->setSystemVersion($list);
                }

                $resultArr['status'] = 1;
                $resultArr['msg'] = '更新成功';
            } else {
                $resultArr['msg'] = '更新失败: ' . $result;
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

    /**
     * 更新本地前端版本号
     *
     * @param $updateList
     */
    private function setSystemVersion($updateList)
    {
        $version = $this->getFrontVersion($updateList);

        $str = file_get_contents(base_path('config/') . 'front-version.php');
        $str = preg_replace('/"[\d\.]+"/', '"'. $version . '"', $str);
        file_put_contents(base_path('config/') . 'front-version.php', $str);
    }

    /**
     * 获取前端版本号
     *
     * @param $updateList
     * @return mixed
     */
    private function getFrontVersion($updateList)
    {
        rsort($updateList);
        $version = $updateList[0]['version']->getVersion();

        return $version;
    }
}
