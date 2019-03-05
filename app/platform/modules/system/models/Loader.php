<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/1
 * Time: 17:39
 */
namespace app\platform\modules\system\models;


use app\common\models\BaseModel;

//function table($name)
//{
//    $table_classname = "\\We7\\Table\\";
//    $subsection_name = explode('_', $name);
//    if (count($subsection_name) == 1) {
//        $table_classname .= ucfirst($subsection_name[0]) . "\\" . ucfirst($subsection_name[0]);
//    } else {
//        foreach ($subsection_name as $key => $val) {
//            if ($key == 0) {
//                $table_classname .= ucfirst($val) . '\\';
//            } else {
//                $table_classname .= ucfirst($val);
//            }
//        }
//    }
//
//    if (in_array($name, array(
//        'modules_rank',
//        'modules_bindings',
//        'modules_plugin',
//        'modules_cloud',
//        'modules_recycle',
//        'modules',
//        'modules_ignore',
//        'account_xzapp',
//        'uni_account_modules',
//    ))) {
//        return new $table_classname;
//    }
//
//    load()->classs('table');
//    load()->table($name);
//    $service = false;
//
//    $class_name = "{$name}Table";
//    if (class_exists($class_name)) {
//        $service = new $class_name();
//    }
//    return $service;
//}


class Loader extends BaseModel
{
    private $cache = array();
    private $singletonObject = array();
    private $libraryMap = array(
        'agent' => 'agent/agent.class',
        'captcha' => 'captcha/captcha.class',
        'pdo' => 'pdo/PDO.class',
        'qrcode' => 'qrcode/phpqrcode',
        'ftp' => 'ftp/ftp',
        'pinyin' => 'pinyin/pinyin',
        'pkcs7' => 'pkcs7/pkcs7Encoder',
        'json' => 'json/JSON',
        'phpmailer' => 'phpmailer/PHPMailerAutoload',
        'oss' => 'alioss/autoload',
        'qiniu' => 'qiniu/autoload',
        'cos' => 'cosv4.2/include',
        'cosv3' => 'cos/include',
        'sentry' => 'sentry/Raven/Autoloader',
    );
    private $loadTypeMap = array(
        'func' => '/framework/function/%s.func.php',
        'model' => '/framework/model/%s.mod.php',
        'classs' => '/framework/class/%s.class.php',
        'library' => '/framework/library/%s.php',
        'table' => '/framework/table/%s.table.php',
        'web' => '/web/common/%s.func.php',
        'app' => '/app/common/%s.func.php',
    );

    public function __construct()
    {
        $this->registerAutoload();
    }

    public function registerAutoload()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class)
    {
        $section = array(
            'Table' => '/framework/table/',
        );
        $classmap = array(
            'We7Table' => 'table',
        );
        if (isset($classmap[$class])) {
            load()->classs($classmap[$class]);
        } elseif (preg_match('/^[0-9a-zA-Z\-\\\\_]+$/', $class)
            && (stripos($class, 'We7') === 0 || stripos($class, '\We7') === 0)) {
            $group = explode("\\", $class);
            $path = IA_ROOT . $section[$group[1]];
            unset($group[0]);
            unset($group[1]);
            $path .= implode('/', $group) . '.php';
            if(is_file($path)) {
                include $path;
            }
        }
    }

    public function __call($type, $params)
    {
        global $_W;
        $name = $cachekey = array_shift($params);
        if (!empty($this->cache[$type]) && isset($this->cache[$type][$cachekey])) {
            return true;
        }
        if (empty($this->loadTypeMap[$type])) {
            return true;
        }
        if ($type == 'library' && !empty($this->libraryMap[$name])) {
            $name = $this->libraryMap[$name];
        }
        $file = sprintf($this->loadTypeMap[$type], $name);
        if (file_exists(IA_ROOT . $file)) {
            include IA_ROOT . $file;
            $this->cache[$type][$cachekey] = true;
            return true;
        } else {
            trigger_error('Invalid ' . ucfirst($type) . $file, E_USER_WARNING);
            return false;
        }
    }


    public function singleton($name)
    {
        if (isset($this->singletonObject[$name])) {
            return $this->singletonObject[$name];
        }
        $this->singletonObject[$name] = $this->object($name);
        return $this->singletonObject[$name];
    }


    public function object($name)
    {
        $this->classs(strtolower($name));
        if (class_exists($name)) {
            return new $name();
        } else {
            return false;
        }
    }
}