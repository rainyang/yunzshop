<?php
namespace app\api\controller\cloud;
@session_start();
use app\api\YZ;

class Upgrade extends YZ
{

    public function index()
    {

        return $this->returnSuccess();
    }

}

