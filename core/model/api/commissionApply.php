<?php
/**
 * 商品model
 *
 * 管理后台 APP API 商品model
 *
 * @package   订单模块
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace model\api;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require __API_ROOT__ . '/../../plugin/commission/model.php';

class commissionApply extends \CommissionModel
{
    protected $name_map = array(
        'status' => array(
            "" => "",

        )
    );

    public function __construct()
    {

    }

    public function getList($para){
        $condition[] = 'WHERE 1';
        $params = array();

        $status = $para['status'];

        $condition['other'] = ' and a.uniacid=:uniacid and a.status=:status';
        if(isset($para['id'])&&!empty($para['id'])){
            $condition['id'] = ' AND a.id<:id';
            $params += array(':id' => $para['id']);
        }
        $params += array(':uniacid' => $para['uniacid'], ':status' => $status);

        if ($status >= 3) {
            $orderby = 'paytime';
        } else if ($status >= 2) {
            $orderby = ' checktime';
        } else {
            $orderby = ' applytime';
        }
        $condition_str = implode(' ', $condition);

        //realname,price,type,time
        $sql = 'select a.id as commission_apply_id,a.status,a.commission,m.avatar,m.realname,applytime,checktime,invalidtime,paytime,type from ' . tablename('sz_yi_commission_apply') . ' a ' . ' left join ' . tablename('sz_yi_member') . ' m on m.id = a.mid' . ' left join ' . tablename('sz_yi_commission_level') . ' l on l.id = m.agentlevel' . " {$condition_str} ORDER BY {$orderby} desc ";

        $list = pdo_fetchall($sql, $params);
        foreach ($list as &$row) {
            $row = $this->formatInfo($row);
        }
        return $list;
    }
    private function formatInfo($row)
    {
        $row['applytime'] = ($row['status'] >= 1 || $row['status'] == -1) ? date('Y-m-d H:i', $row['applytime']) : '--';
        $row['checktime'] = $row['status'] >= 2 ? date('Y-m-d H:i', $row['checktime']) : '--';
        $row['paytime'] = $row['status'] >= 3 ? date('Y-m-d H:i', $row['paytime']) : '--';
        $row['invalidtime'] = $row['status'] == -1 ? date('Y-m-d H:i', $row['invalidtime']) : '--';
        $row['typestr'] = empty($row['type']) ? '余额' : '微信';
        return $row;
    }

}
