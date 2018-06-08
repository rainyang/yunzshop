<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/16
 * Time: 15:55
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\services\notice\WechatApi;
use app\common\models\TemplateMessageDefault;
use app\common\models\notice\MessageTemp;


class DefaultNoticeController extends BaseController
{
    private $WechatApiModel;
    private $TemplateModel;

    public function __construct() {
        $this->WechatApiModel = new WechatApi();
        $this->TemplateModel = new TemplateMessageDefault();
    }

    public function index() {
        $notice_name = \YunShop::request()->notice_name;
        $setting_name = \YunShop::request()->setting_name;
        $notice = \Setting::get($setting_name);
        $temp_model = new MessageTemp();
        $tem_id = $temp_model->getTempIdByNoticeType($notice_name);
        if ($tem_id){
            $notice[$notice_name] = (string)$tem_id;
        } else {
            foreach(\Config::get('notice-template') as $key => $item) {
                if ($key == $notice_name) {
                    $template_id_short = $item['template_id_short'];
                    unset($item['template_id_short']);
                    $template_default_data1 = $item;
                }
            }
            $template_data = $this->TemplateModel->getData($template_id_short);
            if (!$template_data) {
                $template_id = $this->WechatApiModel->getTemplateIdByTemplateIdShort($template_id_short);
                if (empty($template_id)) {
                    echo json_encode([
                        'result' => '0',
                        'msg' => '获取微信模版失败',
                    ]);
                }
                $this->TemplateModel->template_id_short = $template_id_short;
                $this->TemplateModel->template_id = $template_id;
                $this->TemplateModel->uniacid = \YunShop::app()->uniacid;
                $this->TemplateModel->save();
                $template_data['template_id'] = $template_id;
            }
            $template_default_data2 = [
                'uniacid' => \YunShop::app()->uniacid,
                'template_id' => $template_data['template_id'],
                'is_default' => 1,
                'notice_type' => $notice_name,
            ];
            $template_default_data = array_merge($template_default_data1, $template_default_data2);

            $ret = $temp_model::create($template_default_data);
            $notice[$notice_name] = (string)$ret->id;

        }
        \Setting::set($setting_name, $notice);
        echo json_encode([
            'result' => '1',
            'id' => $notice[$notice_name],
        ]);
    }

    public function cancel() {
        $notice_name = \YunShop::request()->notice_name;
        $setting_name = \YunShop::request()->setting_name;
        $notice = \Setting::get($setting_name);
        $notice[$notice_name] = "0";
        \Setting::set($setting_name, $notice);
        echo json_encode([
            'result' => '1',
        ]);
    }

    public function store()
    {
        $notice_name = \YunShop::request()->notice_name;
        $setting_name = \YunShop::request()->setting_name;
        $temp_model = new MessageTemp();
        $tem_id = $temp_model->getTempIdByNoticeType($notice_name);
        if ($tem_id){
            $item = (string)$tem_id;
        } else {
            foreach(\Config::get('notice-template') as $key => $item) {
                if ($key == $notice_name) {
                    $template_id_short = $item['template_id_short'];
                    unset($item['template_id_short']);
                    $template_default_data1 = $item;
                }
            }
            $template_data = $this->TemplateModel->getData($template_id_short);
            if (!$template_data) {
                $template_id = $this->WechatApiModel->getTemplateIdByTemplateIdShort($template_id_short);
                $this->TemplateModel->template_id_short = $template_id_short;
                $this->TemplateModel->template_id = $template_id;
                $this->TemplateModel->uniacid = \YunShop::app()->uniacid;
                $this->TemplateModel->save();
                $template_data['template_id'] = $template_id;
            }
            $template_default_data2 = [
                'uniacid' => \YunShop::app()->uniacid,
                'template_id' => $template_data['template_id'],
                'is_default' => 1,
                'notice_type' => $notice_name,
            ];
            $template_default_data = array_merge($template_default_data1, $template_default_data2);

            $ret = $temp_model::create($template_default_data);
            $item = $ret->id;

        }
        \Setting::set($setting_name, $item);
        $setting = explode('.',$setting_name);
        if($setting[0] == 'love') {
            \Cache::forget('plugin.love.set_' . \YunShop::app()->uniacid);
        }
        echo json_encode([
            'result' => '1',
            'id' => (string)$item,
        ]);
    }

    public function storeCancel() {
        $setting_name = \YunShop::request()->setting_name;
        $item = "0";
        \Setting::set($setting_name, $item);
        $setting = explode('.',$setting_name);
        if($setting[0] == 'love') {
            \Cache::forget('plugin.love.set_' . \YunShop::app()->uniacid);
        }
        echo json_encode([
            'result' => '1',
        ]);
    }

}