<?php
/**
 * 管理后台APP API分销商
 *
 * PHP version 5.6.15
 *
 * @package
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\commission;
class Apply extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        //$this->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
        $status = $para['status'];
        if ($status == -1) {
            $this->ca('commission.apply.view_1');
        } else {
            $this->ca('commission.apply.view' . $status);
        }
        $condition = ' and a.uniacid=:uniacid and a.status=:status';
        $params = array(':uniacid' => $para['uniacid'], ':status' => $status);

        if ($status >= 3) {
            $orderby = 'paytime';
        } else if ($status >= 2) {
            $orderby = ' checktime';
        } else {
            $orderby = 'applytime';
        }
        $sql = 'select a.*, m.nickname,m.avatar,m.realname,m.mobile,l.levelname from ' . tablename('sz_yi_commission_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.mid' . ' left join ' . tablename('sz_yi_commission_level') . ' l on l.id = m.agentlevel' . " where 1 {$condition} ORDER BY {$orderby} desc ";

        $list = pdo_fetchall($sql, $params);
        foreach ($list as &$row) {
            $row['levelname'] = empty($row['levelname']) ? (empty($this->set['levelname']) ? '普通等级' : $this->set['levelname']) : $row['levelname'];
            $row['applytime'] = ($status >= 1 || $status == -1) ? date('Y-m-d H:i', $row['applytime']) : '--';
            $row['checktime'] = $status >= 2 ? date('Y-m-d H:i', $row['checktime']) : '--';
            $row['paytime'] = $status >= 3 ? date('Y-m-d H:i', $row['paytime']) : '--';
            $row['invalidtime'] = $status == -1 ? date('Y-m-d H:i', $row['invalidtime']) : '--';
            $row['typestr'] = empty($row['type']) ? '余额' : '微信';
        }
        unset($row);
        dump($list);
        $this->returnSuccess($list);
    }
}
