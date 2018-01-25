<?php
/**
 * Created 
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/24
 * Time: 下午1:43
 */

namespace app\frontend\modules\goods\controllers;


use app\common\components\ApiController;
use app\common\models\Goods;

/**
 * 商品海报
 */
class GoodsPosterController extends ApiController
{
    
    private $shopSet;
    private $goodsModel;

    private $shopText = [
        'left' => 310,
        'top'  => 90
    ];

    private $goodsText = [
        'left' => 80,
        'top' => 830,
    ];

    public function generateGoodsPoster()
    {
        $id = intval(\YunShop::request()->id);

        if (!$id) {
            return $this->errorJson('请传入正确参数.');
        }

        $this->shopSet = \Setting::get('shop.shop');

        $this->goodsModel = Goods::uniacid()->with('hasOneShare')->find($id);

        $imgPath = $this->get_lt();
        
        $urlPath =  request()->getSchemeAndHttpHost() . '/' . substr($imgPath, strpos($imgPath, 'addons'));
        
        return $this->successJson('ok', $urlPath);

    }

    /**
     * 生成商品海报
     * @return string 商品海报绝对路径
     */
    public function get_lt()
    {   

        set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $target = imagecreatetruecolor(640, 1008);
        $white  = imagecolorallocate($target, 255, 255, 255);
        $color  = imagecolorallocate($target, 226, 226, 226);
        //设置白色背景色
        imagefill($target,0,0,$white);
        //设置线条
        imageline( $target, 0, 130, 640, 130, $color);

        $shopLogo = imagecreatefromstring(file_get_contents(yz_tomedia($this->shopSet['logo'])));

        if ($this->goodsModel->hasOneShare->share_thumb) {

            $goodsThumb = imagecreatefromstring(file_get_contents(yz_tomedia($this->goodsModel->hasOneShare->share_thumb)));

        } else {

            $goodsThumb = imagecreatefromstring(file_get_contents($this->goodsModel->thumb));
        }
        $target = $this->mergeGoodsImage($target, $goodsThumb);
        
        //商品二维码
        $goodsQr =  $this->generateQr();

        $target = $this->mergeLogoImage($target, $shopLogo);
        $target = $this->mergeQrImage($target, $goodsQr);


        if ($this->goodsModel->hasOneShare->share_title) {
            $text = $this->goodsModel->hasOneShare->share_title;
        } else {
            $text = $this->goodsModel->title;
        }
        $target = $this->mergeText($target, $this->goodsText, $text);
        $target = $this->mergeText($target, $this->shopText, $this->shopSet['name']);

        $priceImg = $this->generatePriceImgage();

        $target = $this->mergePriceImage($target, $priceImg);


        imagepng($target, $this->getGoodsPosterPath());
        imagedestroy($target);

        return $this->getGoodsPosterPath();

    }

    private function getGoodsPosterPath()
    {
        $path = storage_path('app/public/goods/'.\YunShop::app()->uniacid) . "/";
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $file_name = \YunShop::app()->uniacid.'-'.$this->goodsModel->id.'.png';
        return $path . $file_name;
    }

    /**
     * 合并商品图片到 $target
     * @param $target
     * @param $img
     * @return mixed
     */
    private function mergeGoodsImage($target, $img)
    {
        $width  = imagesx($img);
        $height = imagesy($img);

        $narrowW = 630 > $width ? $width : 550;
        $narrowH = 700 > $height ? $height : 550;
        imagecopyresized($target, $img, (640-$narrowW) / 2, (900-$narrowH) / 2, 0, 0, $narrowW, $narrowH, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并商城Logo 到 $target
     * @param [type] $target [description]
     * @param [type] $img    [description]
     */
    private function mergeLogoImage($target, $img)
    {
        $width  = imagesx($img);
        $height = imagesy($img);
        imagecopyresized($target, $img, 250, 60, 0, 0, 50, 50, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并商品价格图片
     * @param  [type] $target [description]
     * @param  [type] $img    [description]
     * @return [type]         [description]
     */
    private function mergePriceImage($target, $img)
    {
        $width  = imagesx($img);
        $height = imagesy($img);
        imagecopy($target, $img, 80, 900, 0, 0, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并商品二维码 到 $target
     * @param [type] $target [description]
     * @param [type] $img    [description]
     */
    private function mergeQrImage($target, $img)
    {
        $width  = imagesx($img);
        $height = imagesy($img);
        imagecopy($target, $img, 440, 800, 0, 0, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并名称
     * @param $target
     * @param $params
     * @param $text
     * @return mixed
     */
    private function mergeText($target, $params, $text)
    {
        putenv('GDFONTPATH='.IA_ROOT.'/addons/yun_shop/static/fonts');
        $font = "source_han_sans";
        // $font="c:/windows/fonts/simhei.ttf";

        $text = $this->autowrap(16, 0, $font, $text, 320);


        $black = imagecolorallocate($target,  51, 51, 51);//文字颜色
        imagettftext($target, 16, 0, $params['left'], $params['top'], $black, $font, $text);


        return $target;
    }

    /**
     * 合并商品名称
     * @param $target
     * @param $params
     * @param $text
     * @return mixed
     */
    // private function mergeGoodsText($target)
    // {
    //     if ($this->goodsModel->hasOneShare->share_title) {
    //         $text = $this->goodsModel->hasOneShare->share_title;
    //     } else {
    //         $text = $this->goodsModel->title;
    //     }
    //     $font="c:/windows/fonts/simhei.ttf";
    //         $text = $this->autowrap(14, 0, $font, $text, 198);

    //         $text = $text."\n\n￥".$this->goodsModel->price;
    //     // putenv('GDFONTPATH='.IA_ROOT.'/addons/yun_shop/static/fonts');
    //     // $font = "source_han_sans";
    //     $black = imagecolorallocate($target,  51, 51, 51);//文字颜色
    //     imagettftext($target, 16, 0, 80, 830, $black, $font, $text);


    //     return $target;
    // }

    /**
     * 生成商品价格图
     * @return [type] [description]
     */
    private function generatePriceImgage()
    {
        $priceImg = imagecreatetruecolor(250, 60);
        $white  = imagecolorallocate($priceImg, 255, 255, 255);

        $color  = imagecolorallocate($target, 107, 107, 107);
        //设置白色背景色
        imagefill($priceImg,0,0,$white);
        //设置线条
        imageline($priceImg, 150, 23, 250, 25, $color);
            

        putenv('GDFONTPATH='.IA_ROOT.'/addons/yun_shop/static/fonts');
        $font = "source_han_sans";

            // $font="c:/windows/fonts/simhei.ttf";
            
        $price = '￥'.$this->goodsModel->price;
        $market_price = '￥'.$this->goodsModel->market_price;
        $black = imagecolorallocate($priceImg, 241,83,83);//当前价格颜色

        $gray = imagecolorallocate($priceImg, 107,107,107);//原价颜色


        imagettftext($priceImg, 20, 0, 0, 30, $black, $font, $price);
        imagettftext($priceImg, 14, 0, 150, 30, $gray, $font, $market_price);

        // imagedestroy($priceImg);

        return $priceImg;
        
    }

    /**
     * 生成商品二维码
     * @return [type] [description]
     */
    private function generateQr()
    {
// /'.\YunShop::app()->uniacid
        $path = storage_path('app/public/goods/qrcode/'.\YunShop::app()->uniacid);
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $file = $this->goodsModel->id.'.png';

        if (!is_file($path.'/'.$file)) {

            \QrCode::format('png')->size(150)->generate(yzAppFullUrl('goods/'.$this->goodsModel->id), $path.'/'.$file);
        }

        $img = imagecreatefromstring(file_get_contents($path.'/'.$file));

        return $img;
    }


    /**
     * 字体换行
     * @param  [int] $fontsize [字体大小]
     * @param  [int] $angle    [角度]
     * @param  [string] $fontface [字体名称]
     * @param  [string] $string   [字符串]
     * @param  [int] $width    [预设宽度]
     * @return [string]           [处理好的字符串]
     */
    private function autowrap($fontsize, $angle, $fontface, $string, $width) 
    {
        $content = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i=0;$i<mb_strlen($string);$i++) {
            $letter[] = mb_substr($string, $i, 1);
        }
        foreach ($letter as $l) {
            $teststr = $content." ".$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }



}
