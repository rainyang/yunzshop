<?php
namespace app\api\controller\util;
@session_start();
use app\api\YZ;
use app\api\Request;

class Uploader extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $_W['ispost']= true;
        $result = $this->callMobile('util/uploader/upload');
        //dump($result);exit;
        if($result['code'] == -1){
            $this->returnError($result['json']);
        }
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function upload()
    {
        $this->returnSuccess($this->json);
    }
}

