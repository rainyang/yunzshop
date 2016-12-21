<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;

class Log extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('supplier/logg');
        foreach ($result['json']['list'] as &$log) {
            if ($log['status'] == 0) {
                $log['statusr'] = '申请时间:';
            } else if ($log['status'] == 1) {
                $log['statusr'] = '打款时间:';
            }
        }
        unset($log);
        $result['json']['navs'] = array(
            '0' => array("text" => "全部", "status" => ""),
            '1' => array("text" => "待审核", "status" => "0"),
            '2' => array("text" => "已打款", "status" => "1"),
        );
        $this->returnSuccess($result);
    }
}