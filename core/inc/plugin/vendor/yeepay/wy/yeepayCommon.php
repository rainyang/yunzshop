<?php
include 'HttpClient.class.php';
 	#ʱ������
	date_default_timezone_set('PRC');
	#֧�����󡢳�ֵ���˿��ѯ�ӿڵ�ַ
	$reqURL_onLine = "https://www.yeepay.com/app-merchant-proxy/node";
	#������ѯ���˿����
  $OrderURL_onLine="https://cha.yeepay.com/app-merchant-proxy/command";

#��Ӧ����ת��������
function getresp($respdata)
{
	  $result = explode("\n",urldecode($respdata));
	  $output = array();

    foreach ($result as $data) 
    {
    $arr = explode('=',$data);
    $output[$arr[0]] = $arr[1];
    }
 return $output;
}

#���ɱ���ǩ��hmac(�������ڻص�֪ͨ)
function HmacLocal($data)
{
	$text="";
	global $merchantKey;
	while (list($key,$value) = each($data))
        {
            if(isset($key) && $key!="hmac" && $key!="hmac_safe") 
            {   
            	  
                $text .=    $value;
            }
            
        }
         
         //echo "</br>".$text;
        return HmacMd5($text,$merchantKey);
        
}   
 

//���ɱ��صİ�ȫǩ������
function gethamc_safe($data)
{
	$text="";
	global $merchantKey;
	global $p1_MerId;

	while (list($key,$value) = each($data))
        {
            if( $key!="hmac" && $key!="hmac_safe" && $value !=null)
            {
            	
                $text .=  $value."#" ;
            }
            
        }
        $text1= rtrim( trim($text), '#' ); ; 
        
        //  echo "</br>".$text1;
        
        return HmacMd5($text1,$merchantKey);
        
}  
 

//����hmac

function HmacMd5($data,$key)
{
// RFC 2104 HMAC implementation for php.
// Creates an md5 HMAC.
// Eliminates the need to install mhash to compute a HMAC
// Hacked by Lance Rushing(NOTE: Hacked means written)

//��Ҫ���û���֧��iconv���������Ĳ���������������
$key = iconv("GBK","UTF-8",$key);
$data = iconv("GBK","UTF-8",$data);
$b = 64; // byte length for md5
if (strlen($key) > $b) {
$key = pack("H*",md5($key));
}
$key = str_pad($key, $b, chr(0x00));
$ipad = str_pad('', $b, chr(0x36));
$opad = str_pad('', $b, chr(0x5c));
$k_ipad = $key ^ $ipad ;
$k_opad = $key ^ $opad;

return md5($k_opad . pack("H*",md5($k_ipad . $data)));
}
 


?> 
