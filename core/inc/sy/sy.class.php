<?php
namespace Api;
class SY
{
    public function __construct()
    {

    }
    public function allowCrossOrigin($domain){
        echo header("Access-Control-Allow-Origin: {$domain}");//http://www.sosoapi.com
        echo header('Access-Control-Allow-Credentials: true');
        echo header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        echo header('Access-Control-Allow-Headers: Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With');
    }

}