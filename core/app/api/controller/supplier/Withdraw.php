<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Withdraw extends YZ
{
    private $json;
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('supplier/applyg');
        $this->json = $result['json'];
        $navs = $this->_getSupplierBlockList();
        $this->json['navs'] = $navs;
        $this->returnSuccess($this->json);
    }

    public function apply()
    {
        global $_W;
        $_W['ispost'] = true;
        $result = $this->callPlugin('supplier/applyg');
        $this->json = $result['json'];
        $this->returnSuccess('',$this->json);
    }

    private function _getSupplierBlockList()
    {
        $list = array();
        if (empty($this->json['closetocredit'])) {
            $list[] = array(
                    'id' => 1,
                    'icon' => '',
                    'title' => '提现(线下)',
                    'type' => '1'
            );
        }
        if ($this->json['shopset']['weixin'] == 1) {
            $list[] = array(
                    'id' => 2,
                    'icon' => '',
                    'title' => '提现到微信钱包',
                    'type' => '2'
            );
        }
        return $list;
    }
}