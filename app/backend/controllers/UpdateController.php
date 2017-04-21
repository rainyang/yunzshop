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
    /**
     * 检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $result = ['msg'=>'','last_version'=>'','updated'=>0];

        $update = new AutoUpdate(null, null, 300);
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        //Check for a new update
        if ($update->checkUpdate() === false){
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

    public function get()
    {
        $update = new AutoUpdate(null, null, 300);
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        //Check for a new update
        if ($update->checkUpdate() === false){
            die('Could not check for updates! See log file for details.');
        }

        if ($update->newVersionAvailable()) {
            //Install new update
            echo 'New Version: ' . $update->getLatestVersion() . '<br>';
            echo 'Installing Updates: <br>';

            echo '<pre>';
            var_dump(array_map(function($version) {
                return (string) $version;
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
        return;
    }
}