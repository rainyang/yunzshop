<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 18/04/2017
 * Time: 11:13
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\services\AutoUpdate;

class UpdateController extends BaseController
{

    public function index()
    {
        return view('update.index', [])->render();
    }


    /**
     * 检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $result = ['msg' => '', 'last_version' => '', 'updated' => 0];

        $update = new AutoUpdate(null, null, 300);
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        //Check for a new update
        if ($update->checkUpdate() === false) {
            $result['msg'] = 'Could not check for updates! See log file for details.';
            return response()->json($result)->send();
        }

        if ($update->newVersionAvailable()) {
            $result['last_version'] = $update->getLatestVersion()->getVersion();
            $result['updated'] = 1;
            $result['current_version'] = config('version');
            return response()->json($result)->send();
        }
        return response()->json($result)->send();
    }

    /**
     * 准备下载获取远程url文件大小
     * @return \Illuminate\Http\JsonResponse
     */
    public function prepareDownload()
    {
        $url = '';
        return response()->json(['file_size'=>$this->_getRemoteFileSize($url)]);
    }


    /**
     * 开始下载并更新程序
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startDownload()
    {
        $update = new AutoUpdate(null, null, 300);
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        //Check for a new update
        if ($update->checkUpdate() === false) {
            die('Could not check for updates! See log file for details.');
        }

        if ($update->newVersionAvailable()) {
            //Install new update
            echo 'New Version: ' . $update->getLatestVersion() . '<br>';
            echo 'Installing Updates: <br>';

            echo '<pre>';
            var_dump(array_map(function ($version) {
                return (string)$version;
            }, $update->getVersionsToUpdate()));
            echo '</pre>';


            // This call will only simulate an update.
            // Set the first argument (simulate) to "false" to install the update
            // i.e. $update->update(false);

            $result = $update->update();
            if ($result === true) {
                echo 'Update simulation successful<br>';
            } else {
                echo 'Update simulation failed: ' . $result . '!<br>';
                if ($result = AutoUpdate::ERROR_SIMULATE) {
                    echo '<pre>';
                    var_dump($update->getSimulationResults());
                    echo '</pre>';
                }
            }
        } else {
            echo 'Current Version is up to date<br>';
        }
        return redirect()->back();
    }

    /**
     * ajax检测文件下载进度
     * @return mixed
     */
    public function getFileSize()
    {
        $tmpPath = storage_path('app/auto-update/temp');
        if (file_exists($tmpPath)) {
            // 返回 JSON 格式的响应
            return json(['size' => filesize($tmpPath)])->send();
        }
    }

    /**
     * 获取远程url文件大小
     * @param $url
     * @return int|mixed
     */
    private function _getRemoteFileSize($url)
    {
        // Assume failure.
        $size = -1;

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($curl);
        $size = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($curl);

        return $size;
    }
}