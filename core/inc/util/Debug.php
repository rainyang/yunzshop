<?php
namespace util;
ob_start();

class Debug
{
    const _URL = 'sy.yunzshop.com/test/sy_debug.php';

    //初始化
    public static function __init()
    {
        //php结束后发送日志
        register_shutdown_function(array(get_class(), 'save'));
        //记录函数调用顺序
        register_tick_function(array(get_class(), 'write_dbg_stack'));
        declare(ticks = 1);
        //记录报错
        set_error_handler(array(get_class(), "write_error"));
    }

    public static function write_error($errno, $errstr, $errfile, $errline)
    {
        $GLOBALS['error_info'][] = compact('errno', 'errstr', 'errfile', 'errline');
    }

    public static function write_dbg_stack()
    {
        $GLOBALS['dbg_stack'] = debug_backtrace();
    }

    //获取请求
    public static function getRequest()
    {
        $result['_REQUEST'] = $_REQUEST;
        $result['input'] = file_get_contents("php://input");

        //$result['_SERVER'] = $_SERVER;
        return $result;
    }

    //获取返回
    public static function getRespond()
    {
        //返回正文
        $result = ob_get_contents();
        return $result;
    }

    public static function getDebug()
    {
        //debug信息
        $result = $GLOBALS['dbg_stack'];
        return $result;
    }

    public static function getError()
    {
        $result = $GLOBALS['error_info'];
        return $result;
    }

    //保存到服务器
    public static function save()
    {
        $paramArray = array(
            'request' => self::getRequest(),
            'respond' => self::getRespond(),
            'debug' => self::getDebug(),
            'error' => self::getError(),
        );
        $result = self::_sendRequest(self::_URL, $paramArray, $method = 'POST');
        //var_dump($result);
    }

    /**
     * _sendRequest
     * @param  string $url 请求url
     * @param  array $paramArray 请求参数
     * @param  string $method 请求方法
     * @return
     */
    protected static function _sendRequest($url, $paramArray, $method = 'POST')
    {

        $ch = curl_init();

        if ($method == 'POST') {
            $paramArray = is_array($paramArray) ? http_build_query($paramArray) : $paramArray;
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArray);
        } else {
            $url .= '?' . http_build_query($paramArray);
        }

        //self::$_requestUrl = $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $resultStr = curl_exec($ch);

        //self::$_rawResponse = $resultStr;

        $result = json_decode($resultStr, true);
        if (!$result) {
            return $resultStr;
        }
        return $result;
    }
}
//\sy_debug\Debug::__init();