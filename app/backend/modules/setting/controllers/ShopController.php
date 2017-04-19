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
use app\common\services\MyLink;

class ShopController extends BaseController
{

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid;
    }

    /**
     * 商城设置
     * @return mixed
     */
    public function index()
    {
        $shop = Setting::get('shop.shop');
        $requestModel = \YunShop::request()->shop;
        if ($requestModel) {
            if (Setting::set('shop.shop', $requestModel)) {
                return $this->message('商城设置成功', Url::absoluteWeb('setting.shop.index'));
            } else {
                $this->error('商城设置失败');
            }
        }

        return view('setting.shop.shop', [
            'set' => $shop
        ])->render();
    }

    /**
     * 会员设置
     * @return mixed
     */
    public function member()
    {
        $member = Setting::get('shop.member');
        $requestModel = \YunShop::request()->member;
        if ($requestModel) {
            if (Setting::set('shop.member', $requestModel)) {
                return $this->message('会员设置成功', Url::absoluteWeb('setting.shop.member'));
            } else {
                $this->error('会员设置失败');
            }
        }
        return view('setting.shop.member', [
            'set' => $member
        ])->render();
    }

    /**
     * 模板设置
     * @return mixed
     */
    public function temp()
    {
        $temp = Setting::get('shop.temp');
        $styles = [];//模板数据,数据如何来的待定?
        $styles_pc = [];//pc模板数据,待定
        $requestModel = \YunShop::request()->temp;
        if ($requestModel) {
            if (Setting::set('shop.temp', $requestModel)) {
                return $this->message(' 模板设置成功', Url::absoluteWeb('setting.shop.temp'));
            } else {
                $this->error('模板设置失败');
            }
        }

        return view('setting.shop.temp', [
            'set' => $temp,
            'styles' => $styles,
            'styles_pc' => $styles_pc
        ])->render();
    }

    /**
     * 分类层级设置
     * @return mixed
     */
    public function category()
    {
        $category = Setting::get('shop.category');
        $requestModel = \YunShop::request()->category;
        if ($requestModel) {
            if (Setting::set('shop.category', $requestModel)) {
                return $this->message(' 分类层级设置成功', Url::absoluteWeb('setting.shop.category'));
            } else {
                $this->error('分类层级设置失败');
            }
        }
        return view('setting.shop.category', [
            'set' => $category,
        ])->render();
    }

    /**
     * 联系方式设置
     * @return mixed
     */
    public function contact()
    {
        $contact = Setting::get('shop.contact');
        $requestModel = \YunShop::request()->contact;
        if ($requestModel) {
            if (Setting::set('shop.contact', $requestModel)) {
                return $this->message(' 联系方式设置成功', Url::absoluteWeb('setting.shop.contact'));
            } else {
                $this->error('联系方式设置失败');
            }
        }
        return view('setting.shop.contact', [
            'set' => $contact,
        ])->render();
    }

    /**
     * 短信设置
     * @return mixed
     */
    public function sms()
    {
        $sms = Setting::get('shop.sms');
        $requestModel = \YunShop::request()->sms;
        if ($requestModel) {
            if (Setting::set('shop.sms', $requestModel)) {
                return $this->message(' 短信设置成功', Url::absoluteWeb('setting.shop.sms'));
            } else {
                $this->error('短信设置失败');
            }
        }
        return view('setting.shop.sms', [
            'set' => $sms,
        ])->render();
    }

    /**
     * 分享引导设置
     * @return mixed
     */
    public function share()
    {
        $share = Setting::get('shop.share');
        $requestModel = \YunShop::request()->share;
        if ($requestModel) {
            if (Setting::set('shop.share', $requestModel)) {
                return $this->message(' 引导分享设置成功', Url::absoluteWeb('setting.shop.share'));
            } else {
                $this->error('引导分享设置失败');
            }
        }
        return view('setting.shop.share', [
            'set' => $share,
        ])->render();
    }

    /**
     * 消息提醒设置
     * @return mixed
     */
    public function notice()
    {
        $notice = Setting::get('shop.notice');
        $salers = []; //订单通知的商家列表,数据如何取待定?
        $new_type = []; //通知方式的数组,数据如何来的待定?
        $requestModel = \YunShop::request()->notice;
        if ($requestModel) {
            if (Setting::set('shop.notice', $requestModel)) {
                return $this->message(' 消息提醒设置成功', Url::absoluteWeb('setting.shop.notice'));
            } else {
                $this->error('消息提醒设置失败');
            }
        }
        return view('setting.shop.notice', [
            'set' => $notice,
            'salers' => $salers,
            'new_type' => $new_type
        ])->render();
    }

    /**
     * 交易设置
     * @return mixed
     */
    public function trade()
    {
        $trade = Setting::get('shop.trade');
        $requestModel = \YunShop::request()->trade;
        if ($requestModel) {
            if (Setting::set('shop.trade', $requestModel)) {
                return $this->message(' 交易设置成功', Url::absoluteWeb('setting.shop.trade'));
            } else {
                $this->error('交易设置失败');
            }
        }
        return view('setting.shop.trade', [
            'set' => $trade,
        ])->render();
    }

    /**
     * 支付方式设置
     * @return mixed
     */
    public function pay()
    {
        $pay = Setting::get('shop.pay');

        $data = [
            'weixin_jie_cert' => '',
            'weixin_jie_key' => '',
            'weixin_jie_root' => ''
        ];//借用微信支付证书,在哪里取得数据待定?
        $requestModel = \YunShop::request()->pay;

        if ($requestModel) {

            if ($_FILES['weixin_cert_file']['name']) {
                $file_name = $this->upload('weixin_cert_file');
                if (!empty($file_name)) {
                    $requestModel['weixin_cert'] = storage_path('cert/' . $file_name);
                }
            }

            if ($_FILES['weixin_key_file']['name']) {
                $file_name = $this->upload('weixin_key_file');
                if (!empty($file_name)) {
                    $requestModel['weixin_key'] = storage_path('cert/' . $file_name);
                }
            }

            if ($_FILES['weixin_root_file']['name']) {
                $file_name = $this->upload('weixin_root_file');
                if (!empty($file_name)) {
                    $requestModel['weixin_root'] = storage_path('cert/' . $file_name);
                }
            }

            if (Setting::set('shop.pay', $requestModel)) {
                $this->setAlipayParams($requestModel);
                return $this->message(' 支付方式设置成功', Url::absoluteWeb('setting.shop.pay'));
            } else {
                $this->error('支付方式设置失败');
            }
        }
        return view('setting.shop.pay', [
            'set' => $pay,
            'data' => $data
        ])->render();
    }

    private function setAlipayParams($data)
    {
        Setting::set('alipay.pem', storage_path() . '/cert/cacert.pem');
        Setting::set('alipay.partner_id', $data['alipay_partner']);
        Setting::set('alipay.seller_id', $data['alipay_account']);
        Setting::set('alipay-mobile.sign_type', 'RSA');
        Setting::set('alipay-mobile.private_key_path', storage_path() . '/cert/private_key.pem');
        Setting::set('alipay-mobile.public_key_path', storage_path() . '/cert/public_key.pem');
        Setting::set('alipay-mobile.notify_url', Url::shopUrl('payment/alipay/notifyUrl.php'));
        Setting::set('alipay-web.key', $data['alipay_secret']);
        Setting::set('alipay-web.sign_type', 'MD5');
        Setting::set('alipay-web.notify_url', Url::shopUrl('payment/alipay/notifyUrl.php'));
        Setting::set('alipay-web.return_url', Url::shopUrl('payment/alipay/returnUrl.php'));
    }


    private function upload($fileinput)
    {
        if (\Request::isMethod('post')) {
            $file = \Request::file($fileinput);

            if ($file->isValid()) {

                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径

                $bool = \Storage::disk('cert')->put($originalName, file_get_contents($realPath));

                return $bool ? $originalName : '';
            }
        }
    }
}