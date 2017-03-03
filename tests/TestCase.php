<?php
namespace tests\app\api;
require_once __DIR__ . '/../vendor/autoload.php';

//use phpunit\phpunit\src\Framework\TestCase;
class TestCase extends \PHPUnit_Framework_TestCase
{

    protected static $default_cookie = array(
        "ext_type" => "1",
        "PHPSESSID" => "d86a943b3f69e3f98789d752720bf677",
        "__cookie_sz_yi_userid_2" => "b1lHaUZ4R0tmT0NMV21TOG8xa2pWX0Nfd1U2SQ",
    );
    protected $base_url = 'http://www.yunzong.com/app_api.php?uniacid=2&api=';

    protected function setUp()
    {
    }

    protected function array2Cookie($array)
    {
        $cookie = array();
        foreach ($array as $key => $value) {
            $cookie[] = "{$key}={$value}";
        };

        $cookie = implode('; ', $cookie);
        return $cookie;
    }

    protected function get($path, $data, $cookie = null)
    {
        if (is_null($cookie)) {
            $cookie = self::$default_cookie;
        }
        $cookie = array2Cookie($cookie);
        function array2Cookie($array){
            $cookie = array();
            foreach ($array as $key => $value) {
                $cookie[] = "{$key}={$value}";
            };

            $cookie = implode('; ', $cookie);
            return $cookie;
        };
        $base_url = $this->base_url;
        $url = $base_url . $path;
        $data = http_build_query($data);
        $url .= "&" . $data;
        //var_dump($url);
        return $this->getCurl($url, $cookie);
    }

    protected function post($path, $data)
    {
        $base_url = $this->base_url;
        $url = $base_url . '/' . $path;
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

    public function getCurl($url, $cookie)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
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
