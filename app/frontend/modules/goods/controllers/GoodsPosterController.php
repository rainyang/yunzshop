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

    private $mid;
    //画布大小
    // private $canvas = [
    //     'width' => 338,
    //     'height' => 485,
    // ];

    private $shopText = [
        'left' => 260,
        'top'  => 65,
        'type' => 0,
    ];

    private $goodsText = [
        'left' => 30,
        'top' => 800,
        'type' => 1,
    ];

    public function generateGoodsPoster()
    {
        $id = intval(\YunShop::request()->id);

        $this->mid = \YunShop::app()->getMemberId();

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
    * 圆角图片
    * @param $radius 角度
    * @return img
    */
    public function get_lt_rounder_corner($radius) {  
        $img     = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像  
        $bgcolor    = imagecolorallocate($img, 1, 1, 1);   // 图像的背景  
        $fgcolor    = imagecolorallocate($img, 0, 0, 0);  
        imagefill($img, 0, 0, $bgcolor);  
        // $radius,$radius：以图像的右下角开始画弧  
        // $radius*2, $radius*2：已宽度、高度画弧  
        // 180, 270：指定了角度的起始和结束点  
        // fgcolor：指定颜色  
        imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);  
        // 将弧角图片的颜色设置为透明  
        imagecolortransparent($img, $fgcolor);  
        return $img;  
    }

    public function roundRadius($resource, $image_width, $image_height, $radius = 8)
    {
        // lt(左上角)  
        $lt_corner  = $this->get_lt_rounder_corner($radius);  

        // header('Content-Type: image/png');  
        // imagepng($lt_corner);  
        // exit;  
        imagecopymerge($resource, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);  
        // lb(左下角)  
        $lb_corner  = imagerotate($lt_corner, 90, 0);  
        imagecopymerge($resource, $lb_corner, 0, $image_height - $radius, 0, 0, $radius, $radius, 100);  
        // rb(右上角)  
        $rb_corner  = imagerotate($lt_corner, 180, 0);  
        imagecopymerge($resource, $rb_corner, $image_width - $radius, $image_height - $radius, 0, 0, $radius, $radius, 100);  
        // rt(右下角)  
        $rt_corner  = imagerotate($lt_corner, 270, 0);  
        imagecopymerge($resource, $rt_corner, $image_width - $radius, 0, 0, 0, $radius, $radius, 100);  
      
        // header('Content-Type: image/png');  
        // imagepng($resource);  
        // exit;  
        return $resource;
    }

    /**
     * 生成商品海报
     * @return string 商品海报绝对路径
     */
    public function get_lt()
    {   


        set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $image_width = 600; //335
        $image_height = 1000; //485

        $target = imagecreatetruecolor($image_width, $image_height);
        $white  = imagecolorallocate($target, 255, 255, 255);
        $color  = imagecolorallocate($target, 226, 226, 226);
        //设置白色背景色
        imagefill($target,0,0,$white);
        //设置线条
        imageline( $target, 0, 100, $image_width, 100, $color);

        $target = $this->roundRadius($target, $image_width, $image_height);

        $shopLogo = imagecreatefromstring(\Curl::to(yz_tomedia($this->shopSet['logo']))->get());
        if ($this->goodsModel->hasOneShare->share_thumb) {

            $goodsThumb = imagecreatefromstring(\Curl::to(yz_tomedia($this->goodsModel->hasOneShare->share_thumb))->get());

        } else {

            $goodsThumb = imagecreatefromstring(\Curl::to(yz_tomedia($this->goodsModel->thumb))->get());
        }
        
        $target = $this->mergeGoodsImage($target, $goodsThumb);
        
        //商品二维码
        $goodsQr =  $this->generateQr();

        $target = $this->mergeLogoImage($target, $shopLogo);


        if ($this->goodsModel->hasOneShare->share_title) {
            $text = $this->goodsModel->hasOneShare->share_title;
        } else {
            $text = $this->goodsModel->title;
        }
        
        $target = $this->mergeText($target, $this->goodsText, $text);
        $target = $this->mergeText($target, $this->shopText, $this->shopSet['name']);

        $target = $this->mergePriceText($target);
       
        $target = $this->mergeQrImage($target, $goodsQr);

        // header ( "Content-type: image/png" );
        // imagePng ( $target );
        // exit();

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

        imagecopyresized($target, $img, 30, 180, 0, 0, 540, 540, $width, $height);
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
        imagecopyresized($target, $img, 200, 25, 0, 0, 50, 50, $width, $height);
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
        imagecopy($target, $img, 400, 750, 0, 0, $width, $height);
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

        if ($params['type']) {
            $text = $this->autowrap(30, 0, $font, $text, 320);
        }

        $black = imagecolorallocate($target,  51, 51, 51);//文字颜色
        imagettftext($target, 30, 0, $params['left'], $params['top'], $black, $font, $text);

        return $target;
    }

    /**
     * 合并商品价格
     * @return [type] [description]
     */
    private function mergePriceText($target)
    {

        $color  = imagecolorallocate($target, 107, 107, 107);

        putenv('GDFONTPATH='.IA_ROOT.'/addons/yun_shop/static/fonts');
        
        $font = "source_han_sans";

        // $font="c:/windows/fonts/simhei.ttf";
            
        $price = '现价:￥'.$this->goodsModel->price;
        $market_price = '原价:￥'.$this->goodsModel->market_price;
        $black = imagecolorallocate($target, 241,83,83);//当前价格颜色

        $price_box = imagettfbbox(18, 0, $font, $price);
        $market_price_box = imagettfbbox(24, 0, $font, $market_price);
        $gray = imagecolorallocate($target, 107,107,107);//原价颜色

        //设置删除线条
        // imageline($target, $price_box[2] + 12, 900, $price_box[2]+$market_price_box[2] + 14, 900, $color);

        imagettftext($target, 24, 0, 30, 910, $black, $font, $price);
        imagettftext($target, 16, 0, 30, 940, $gray, $font, $market_price);

        return $target;
        
    }

    /**
     * 生成商品二维码
     * @return [type] [description]
     */
    private function generateQr()
    {
        $url = yzAppFullUrl('/goods/'.$this->goodsModel->id, ['mid'=> $this->mid]);

        $path = storage_path('app/public/goods/qrcode/'.\YunShop::app()->uniacid);
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $file = 'mid-'.$this->mid.'-goods-'.$this->goodsModel->id.'.png';

        if (!is_file($path.'/'.$file)) {

            \QrCode::format('png')->size(200)->generate($url, $path.'/'.$file);
        }
        $img = imagecreatefromstring(file_get_contents($path.'/'.$file));
        // unlink($path.'/'.$file);

        return $img;
    }


    /**
     * 字体换行
     * @param  [int] $fontsize [字体大小]
     * @param  [int] $angle    [角度]
     * @param  [string] $fontface [字体类型]
     * @param  [string] $string   [字符串]
     * @param  [int] $width    [预设宽度]
     * @return [string]           [处理好的字符串]
     */
    private function autowrap($fontsize, $angle, $fontface, $string, $width) 
    {
        $content = "";
        $num = 0;
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i=0;$i<mb_strlen($string);$i++) {
            $letter[] = mb_substr($string, $i, 1);
        }
        foreach ($letter as $l) {
            $teststr = $content." ".$l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $num += 1;
                if ($num > 1) {
                    $content .= '...';
                    // dd($content);
                    return $content;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }



}
