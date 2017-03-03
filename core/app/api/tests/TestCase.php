<?php
namespace app\api\tests;
require_once '/addons/sz_yi/vendor/autoload.php';
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $base_url = 'http://www.yunzong.com/test/uniacid=2&app_api.php?api=';

    protected function get($path,$data){
        $base_url = $this->base_url;
        $url = $base_url.'/'.$path;
        http_build_query($data);
        return $this->getCurl($url, $data);
    }
    protected function post($path,$data){
        $base_url = $this->base_url;
        $url = $base_url.'/'.$path;
        return $this->getCurlData($url, $data);
    }
    public function getCurlData($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($ch);
        $res = json_decode($out, true);
        curl_close($ch);
        return $res;
    }
    public function getCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        /*
        if (!empty($data)){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        */
        $out = curl_exec($ch);
        $res = json_decode($out, true);
        curl_close($ch);
        return $res;
    }
}
