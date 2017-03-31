<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/7/20
 * Time: 下午5:55
 */

class Aes{
    private $key; //密钥名称
    private $val; //密钥值
    public function __construct($key,$val){
        if($key ){//&& $val){
            $this->key = $key;
            //$this->val = $val;
        }else{
            $this->key = "hrbin-uchat-2015";	//密钥名称
            //$this->val = "ruiyun-rzky-2014";	//密钥值
        }
        $this->out	=	'';
    }

    /**
     * @todo   removePKCS7加密方法
     * @param  string $instr
     * @return string
     */
    public function removePKCS7($instr){
        $imax = strlen($instr);
        for($i = 0 ; $i < $imax ; $i++){
            if(ord($instr[$i])>16)
                $this->out .=$instr[$i];
        }
        return $this->out;
    }
    /**
     * @todo   paddingPKCS7加密方法
     * @param  string $data
     * @return string
     */
    public function paddingPKCS7($data) {
        $block_size = 16;
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char),$padding_char);
        return $data;
    }
    /**
     * @todo   aes加密方法
     * @param  string or array $data
     * @return string
     */
    public function aes_encode($data){
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $this->paddingPKCS7($data), MCRYPT_MODE_ECB,$this->val));
    }
    /**
     * @todo   aes解密方法
     * @param  array or string $data
     * @return array or string
     */
    public function aes_decode($data){
        return  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key,base64_decode($data), MCRYPT_MODE_ECB, $this->val);
    }
    /**
     * @todo	siyuan加密方法
     * @param   string or array $data
     * @return  string or array
     */
    public function siyuan_aes_encode($data){
        return self::aes_encode($data);
    }
    /**
     * @todo  siyuan解密方法
     * @param string or array $data
     */
    public function siyuan_aes_decode($data){
        return self::removePKCS7(self::aes_decode($data));
    }
    //-----------PC验证客户端需要的二维码加密方法-------------------------//
    //加密智码
    static  function strToHex($string){
        $hex="";
        for   ($i=0;$i<strlen($string);$i++)
            $hex.=dechex(ord($string[$i]));
        $hex=strtoupper($hex);
        return   $hex;
    }

    static function swap($hex){
        $string="";
        for   ($i=0;$i<strlen($hex)-1;$i+=2)
            $string.=chr(hexdec($hex[$i+1].$hex[$i]));
        return   $string;
    }

    static function getSecretCode($instr){
        return  'X'.self::swap(self::strToHex($instr));//X为58
    }
}