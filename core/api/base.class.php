<?php
namespace Api;
class Base
{
    protected $para;
    protected $error_info;

    public function __construct()
    {

        /*if(!IS_POST){
            exit('提交方式不正确');
        }*/
        $this->aes = new \Common\Org\Aes();

        $this->para = json_decode(urldecode($this->aes->siyuan_aes_decode(str_replace(" ", "+", $_POST['para']))), TRUE);//
        //$this->addLog();
    }
    public function getPara(){
        return $this->para;
    }
    public function returnSuccess($data = [], $msg = '成功')
    {
        $res = array('result' => '1',
            'msg' => $msg,
            'data' => $data);
        $this->callBackByAes($res);
    }
    public function returnError($msg = '网络繁忙')
    {
        $res = array('result' => '0',
            'msg' => $msg,
            'data' => []);
        $this->callBackByAes($res);
    }
    public function validate($expect_keys){
        $expect_keys = explode(',',$expect_keys);
        if(is_array($this->para)){
            $para_keys = array_keys($this->para);
            $missing_paras = array_diff($expect_keys,$para_keys);
        }
        if(count($missing_paras)>0){
            $missing_paras = implode(',',$missing_paras);
            $this->returnError("缺少参数:{$missing_paras}");
        }
        return true;
    }
    /**
     * @todo    生成经过Aes加密后的字符串
     * @param    array $json_data
     * @return    string
     */
    protected function callBackByAes($json_data)
    {
        if(isset($_GET['is_test'])){
            dump($json_data);
        }
        $return_data = str_replace('"', '', $this->aes->siyuan_aes_encode(json_encode($json_data, JSON_UNESCAPED_UNICODE)));
        //dump($json_data);
        //dump($this->getSqlLog());
        exit($return_data);
    }
    protected function addLog()
    {
        $data['para'] = $this->para=='null' ? '' : json_encode($this->para, JSON_UNESCAPED_UNICODE);
        $data['api'] = $api_name = __INFO__;
        $data['client_ip'] = $this->getClientIp();
        $data['error_info'] = "";
        $data['is_error'] = "";
        D('ApiLog')->add($data);
    }
    public function setErrorInfo($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }
        $this->error_info[] = 'PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;
        if(is_test()){
            dump($this->error_info);
        }
    }
    protected function getClientIp()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        return $cip;
    }
}