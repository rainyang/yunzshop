<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/6
 * Time: 14:01
 */
namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;

class UploadController extends BaseController
{
    public function image()
    {
        $year = request()->year;
        $month = request()->month;
        $page = max(1, intval(request()->page));
        $groupid = intval(request()->groupid);
        $page_size = 24;
        $islocal = request()->local == 'local';

        $is_local_image = $islocal == 'local' ? true : false;
//        $attachment_table = table('attachment');
//        $attachment_table = $attachment_table->local($is_local_image);
//        $attachment_table->searchWithUniacid($uniacid);
//        $attachment_table->searchWithUploadDir($module_upload_dir);

        echo '<pre>';
        print_r($is_local_image);
        exit;

//        if (empty($uniacid)) {
//            $attachment_table->searchWithUid($_W['uid']);
//        }
//        if ($groupid > 0) {
//            $attachment_table->searchWithGroupId($groupid);
//        }
//
//        if ($groupid == 0) {
//            $attachment_table->searchWithGroupId(-1);
//        }
//
//        if ($year || $month) {
//            $start_time = strtotime("{$year}-{$month}-01");
//            $end_time = strtotime('+1 month', $start_time);
//            $attachment_table->searchWithTime($start_time, $end_time);
//        }
//        if ($islocal) {
//            $attachment_table->searchWithType(ATTACH_TYPE_IMAGE);
//        } else {
//            $attachment_table->searchWithType(ATTACHMENT_IMAGE);
//        }
//        $attachment_table->searchWithPage($page, $page_size);
//
//        $list = $attachment_table->searchAttachmentList();
//        $total = $attachment_table->getLastQueryTotal();
//        if (!empty($list)) {
//            foreach ($list as &$meterial) {
//                if ($islocal) {
//                    $meterial['url'] = tomedia($meterial['attachment']);
//                    unset($meterial['uid']);
//                } else {
//                    $meterial['attach'] = tomedia($meterial['attachment'], true);
//                    $meterial['url'] = $meterial['attach'];
//                }
//            }
//        }
//
//        $pager = pagination($total, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => $_W['isajax']));
//        $result = array('items' => $list, 'pager' => $pager);
//        iajax(0, $result);
    }

}