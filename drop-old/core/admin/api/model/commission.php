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
namespace admin\api\model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require __CORE_PATH__ . '/../plugin/commission/model.php';

class commission extends \CommissionModel
{
    protected $name_map = array(
        'status' => array(
            "" => "非法状态",
            "0" => "未审核",
            "1" => "已审核"
        )
    );

    public function __construct()
    {
        parent::__construct('commission');
    }
    public function getList($para)
    {
        $condition[] = 'WHERE 1';
        $params = array();
        if (isset($para['status']) && $para['status'] != '') {
            $condition['status'] = ' AND dm.status=' . intval($para['status']);
        }
        if (isset($para['id']) && !empty($para['id'])) {
            $condition['id'] = "AND dm.id<{$para['id']}";
        }
        $condition['other'] = " AND dm.uniacid = {$para['uniacid']} AND dm.isagent =1 ";
        $condition_str = implode(' ', $condition);

        //todo 从配置中读取
        $default_level_name = '普通等级';
        $sql = "select dm.id as member_id,dm.mobile,dm.realname,dm.nickname,dm.avatar,IFNULL(l.levelname,'{$default_level_name}') as levelname,dm.status from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('sz_yi_commission_level') . " l on l.id = dm.agentlevel" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid and f.uniacid={$para['uniacid']}" . "   {$condition_str} ";
        $sql .= "ORDER BY dm.id DESC,dm.agenttime DESC";
        $list = pdo_fetchall($sql, $params);
        foreach ($list as &$item) {
            $item = $this->formatInfo($item);
        }
        return $list;
    }

    private function formatInfo($info)
    {
        $info['status_value'] = $info['status'];

        $info['status'] = array(
            'name' => $this->name_map['status'][$info['status']],
            'value' => $info['status']
        );
        return $info;
    }

}
