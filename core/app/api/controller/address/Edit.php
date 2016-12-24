<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;

class Edit extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W,$_GPC;
        $_W['ispost'] = true;
        $_GPC['addressdata'] = array_elements(array('realname','mobile','province','city','area','address'),$_GPC);

        $result = $this->callMobile('shop/address/submit');
        $this->variable = $result['variable'];
        $this->json = $result['json'];

    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}