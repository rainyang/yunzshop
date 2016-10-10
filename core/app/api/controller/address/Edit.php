<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Edit extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        global $_W;
        $_W['ispost'] = true;
        $result = $this->callMobile('shop/address/submit');
        $this->variable = $result['variable'];
        $this->json = $result['json'];

    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}