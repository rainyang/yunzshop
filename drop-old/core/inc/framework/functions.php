<?php
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name = null, $value = null, $default = null)
{
    static $_config = array();
// 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
// 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
// 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
// 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}
/**
 * 加载配置文件 支持格式转换 仅支持一级配置
 * @param string $file 配置文件名
 * @param string $parse 配置解析方法 有些格式需要用户自己解析
 * @return array
 */
function load_config($file,$parse=CONF_PARSE){
    $ext  = pathinfo($file,PATHINFO_EXTENSION);
    switch($ext){
        case 'php':
            return include $file;
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml':
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if(function_exists($parse)){
                return $parse($file);
            }else{
                E(L('_NOT_SUPPORT_').':'.$ext);
            }
    }
}
/**
 * 抛出异常处理
 * @param string $msg 异常消息
 * @param integer $code 异常代码 默认为0
 * @throws Think\Exception
 * @return void
 */
function E($msg, $code=0) {
    throw new Think\Exception($msg, $code);
}
/**
 * 获取和设置语言定义(不区分大小写)
 * @param string|array $name 语言变量
 * @param mixed $value 语言值或者变量
 * @return mixed
 */
function L($name=null, $value=null) {
    static $_lang = array();
    // 空参数返回所有定义
    if (empty($name))
        return $_lang;
    // 判断语言获取(或设置)
    // 若不存在,直接返回全大写$name
    if (is_string($name)) {
        $name   =   strtoupper($name);
        if (is_null($value)){
            return isset($_lang[$name]) ? $_lang[$name] : $name;
        }elseif(is_array($value)){
            // 支持变量
            $replace = array_keys($value);
            foreach($replace as &$v){
                $v = '{$'.$v.'}';
            }
            return str_replace($replace,$value,isset($_lang[$name]) ? $_lang[$name] : $name);
        }
        $_lang[$name] = $value; // 语言定义
        return null;
    }
    // 批量定义
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return null;
}
/**
 * 实例化模型类 格式 [资源://][模块/]模型
 * @param string $name 资源地址
 * @param string $layer 模型层名称
 * @return Think\Model
 */
function D($name='',$layer='') {
    if(empty($name)) return new Think\Model;
    static $_model  =   array();
    $layer          =   $layer? : C('DEFAULT_M_LAYER');//'app\\modle';//C('DEFAULT_M_LAYER');
    if(isset($_model[$name.$layer]))
        return $_model[$name.$layer];
    $class          =   'app\\api'.'\\model\\'.$name;//parse_res_name($name,$layer);
    if(class_exists($class)) {
        $model      =   new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        echo 2;
        // 自动加载公共模块下面的模型
        if(!C('APP_USE_NAMESPACE')){
            import('Common/'.$layer.'/'.$class);
        }else{
            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
        }
        dump($class);
        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
    }else {
        Think\Log::record('D方法实例化没找到模型类'.$class,Think\Log::NOTICE);
        $model      =   new Think\Model(basename($name));
    }
    $_model[$name.$layer]  =  $model;
    return $model;
}
/**
 * 解析资源地址并导入类库文件
 * 例如 module/controller addon://module/behavior
 * @param string $name 资源地址 格式：[扩展://][模块/]资源名
 * @param string $layer 分层名称
 * @param integer $level 控制器层次
 * @return string
 */
function parse_res_name($name,$layer,$level=1){
    if(strpos($name,'://')) {// 指定扩展资源
        list($extend,$name)  =   explode('://',$name);
    }else{
        $extend  =   '';
    }
    if(strpos($name,'/') && substr_count($name, '/')>=$level){ // 指定模块
        list($module,$name) =  explode('/',$name,2);
    }else{
        $module =   defined('MODULE_NAME') ? MODULE_NAME : '' ;
    }
    $array  =   explode('/',$name);
    if(!C('APP_USE_NAMESPACE')){
        $class  =   parse_name($name, 1);
        import($module.'/'.$layer.'/'.$class.$layer);
    }else{
        $class  =   $module.'\\'.$layer;
        foreach($array as $name){
            $class  .=   '\\'.parse_name($name, 1);
        }
        // 导入资源类库
        if($extend){ // 扩展资源
            $class      =   $extend.'\\'.$class;
        }
    }
    return $class.$layer;
}
/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}
/**
 * 设置和获取统计数据
 * 使用方法:
 * <code>
 * N('db',1); // 记录数据库操作次数
 * N('read',1); // 记录读取次数
 * echo N('db'); // 获取当前页面数据库的所有操作次数
 * echo N('read'); // 获取当前页面读取次数
 * </code>
 * @param string $key 标识位置
 * @param integer $step 步进值
 * @param boolean $save 是否保存结果
 * @return mixed
 */
function N($key, $step=0,$save=false) {
    static $_num    = array();
    if (!isset($_num[$key])) {
        $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
    }
    if (empty($step)){
        return $_num[$key];
    }else{
        $_num[$key] = $_num[$key] + (int)$step;
    }
    if(false !== $save){ // 保存结果
        S('N_'.$key,$_num[$key],$save);
    }
    return null;
}
/**
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m
 * @return mixed
 */
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(MEMORY_LIMIT_ON && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }

    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
    }
    return null;
}
/**
 * 添加和获取页面Trace记录
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别
 * @param boolean $record 是否记录日志
 * @return void|array
 */
function trace($value='[think]',$label='',$level='DEBUG',$record=false) {
    return Think\Think::trace($value,$label,$level,$record);
}