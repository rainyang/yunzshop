<?php
/**
 *  应答辅助类
 */
class GatewayNotify  {

    /** 商户密钥 */
    var $key;

    /** 应答的参数 */
    var $parameters;

    /** 调试信息 */
    var $debugMsg;

    function __construct() {
        $this->GatewayNotify();
    }

    function GatewayNotify() {
        $this->key = "";
        $this->parameters = array();
        $this->debugMsg = "";

        foreach($_GET as $k => $v) {
            $this->setParameter($k, $v);
        }
        foreach($_POST as $k => $v) {
            $this->setParameter($k, $v);
        }
    }

    function getKey() {
        return $this->key;
    }


    function setKey($key) {
        $this->key = $key;
    }


    function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     *获取全部参数
     *@return array
     */
    function getAllParameters() {
        return $this->parameters;
    }

    /**
     *使用SHA256算法验证签名。规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     *true:是
     *false:否
     */
    function verifySign() {
        $signPars = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->getKey();

        $sign = strtolower(hash("sha256",$signPars));

        $tenpaySign = strtolower($this->getParameter("sign"));

        //debug信息
        $this->setDebugMsg($signPars . " => sign:" . $sign .
            " epaylinksSign:" . $this->getParameter("sign"));

        return $sign == $tenpaySign;

    }

    /**
     *  调试信息
     */
    function getDebugMsg() {
        return $this->debugMsg;
    }

    /**
     *  调试信息
     */
    function setDebugMsg($debugMsg) {
        $this->debugMsg = $debugMsg;
    }

}


?>