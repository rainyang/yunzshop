<?php
/**
 * 管理后台APP API接口基类
 *
 * @package   管理后台APP API
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */

namespace app\api;
//require_once __DIR__ . '/base.php';

class Request
{
    private static $para;

    public static function initialize($para = null)
    {
        if (isset($para)) {
            return self::$para = $para;
        }

        if (!empty($_POST)) {
            self::$para = $_POST;
        } else {
            self::$para = json_decode(file_get_contents("php://input"), true);
        }
        self::$para = self::$para ? self::$para : array();

        self::$para = array_merge(self::$para, $_GET);
        return self;
    }


    public static function has($key)
    {
        $bool = in_array($key, self::toArray());
        return $bool;
    }
    
    public static function all()
    {
        $para = self::$para;
        return $para;
    }

    public static function query($key,$default_value='')
    {
        $para =  $_GET;
        if(!isset($para[$key])){
            return $default_value;
        }
        return $para[$key];
    }

    public static function input($key,$default_value='')
    {
        $para = (array)self::$para;
        if(!isset($para[$key])){
            return $default_value;
        }
        return $para[$key];
    }

    public static function only($keys)
    {
        return array_part($keys, self::$para);
    }

    /**
     * 验证请求参数是否完整
     *
     * 详细描述（略）
     * @param string $validate_fields ','连接的参数名
     * @return bool 参数是否完整
     */
    public static function validate($validate_fields)
    {
        $para = self::$para;

        $message = '';
        foreach ($validate_fields as $field_name => $field_info) {
            switch ($field_info['type']) {
                case '':
                    break;
            }
            if (!(isset($field_info['required']) && $field_info['required'] === false)) {
                $para_value = $para[$field_name];
                if (!(isset($para_value) && !empty($para_value))) {
                    $message .= "{$field_info['describe']}不能为空!";
                }
            }
        }
        return $message;
    }

    public static function filter($validate_fields)
    {
/*        $para = self::$para;

        foreach ($para as $para_name => $para_value) {
            $validate_keys = array_keys($validate_fields);
            if (!in_array($para_name, $validate_keys)) {
                unset($para['$para_name']);
            }
        }*/
    }

    public static function toArray()
    {
        $para = (array)self::$para;
        return $para;
    }
}