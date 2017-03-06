<?php
/**
 * 支付请求提交类
 */
class GatewaySubmit {

    /** 网关url地址 */
    var $gateUrl;

    /** 密钥 */
    var $key;

    /** 请求的参数 */
    var $parameters;

    /** 调试信息 */
    var $debugMsg;

    function __construct() {
        $this->GatewaySubmit();
    }

    function GatewaySubmit() {
        $this->gateUrl = "https://www.epaylinks.cn/paycenter/v2.0/getoi.do";
        $this->key = "";
        $this->parameters = array();
        $this->debugMsg = "";
    }

    /**
     *获取入口地址,不包含参数值
     */
    function getGateURL() {
        return $this->gateUrl;
    }

    /**
     *设置入口地址,不包含参数值
     */
    function setGateURL($gateUrl) {
        $this->gateUrl = $gateUrl;
    }

    /**
     *获取密钥
     */
    function getKey() {
        return $this->key;
    }

    /**
     *设置密钥
     */
    function setKey($key) {
        $this->key = $key;
    }

    /**
     *获取参数值
     */
    function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    /**
     *设置参数值
     */
    function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     *获取所有请求的参数
     *@return array
     */
    function getAllParameters() {
        return $this->parameters;
    }

    /**
     *获取带参数的请求URL
     */
    function getRequestURL() {

        $this->buildRequestSign();

        $reqPar = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            $reqPar .= $k . "=" . urlencode($v) . "&";
        }

        //去掉最后一个&
        $reqPar = substr($reqPar, 0, strlen($reqPar)-1);

        $requestURL = $this->getGateURL() . "?" . $reqPar;

        return $requestURL;

    }

    /**
     *获取调试信息
     */
    function getDebugMsg() {
        return $this->debugMsg;
    }

    /**
     *重定向到支付
     */
    function doSend() {
        header("Location:" . $this->getRequestURL());
        exit;
    }

    /**
     *生成SHA256摘要,规则是:按ASCII码顺序排序,遇到空值的参数不参加签名。
     */
    function buildRequestSign() {
        $signOrigStr = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signOrigStr .= $k . "=" . $v . "&";
            }
        }
        $signOrigStr .= "key=" . $this->getKey();
        $sign = strtolower(hash("sha256",$signOrigStr));
        $this->setParameter("sign", $sign);

        //调试信息
        $this->_setDebugMsg($signOrigStr . " => sign:" . $sign);

    }

    /**
     *设置调试信息
     */
    function _setDebugMsg($debugMsg) {
        $this->debugMsg = $debugMsg;
    }

}

?>