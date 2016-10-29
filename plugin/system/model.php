<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('SystemModel')) {
    class SystemModel extends PluginModel
    {
        public function get_wechats()
        {
            return pdo_fetchall("SELECT  a.uniacid,a.name FROM " . tablename('account_wechats') . " a  " . " left join " . tablename('sz_yi_sysset') . " s on a.uniacid = s.uniacid");
        }
        public function getCopyright()
        {
            global $_W;
            $copyrights = m('cache')->getArray('systemcopyright', 'global');
            if (!is_array($copyrights)) {
                $copyrights = pdo_fetchall('select *  from ' . tablename('sz_yi_system_copyright'), array(), 'uniacid');
                m('cache')->set('systemcopyright', $copyrights, 'global');
            }
            $copyright = false;
            if (isset($copyrights[$_W['uniacid']])) {
                $copyright = $copyrights[$_W['uniacid']];
            } else if (isset($copyrights[-1])) {
                $copyright = $copyrights[-1];
            }
            return $copyright;
        }
        
        function perms()
        {
            $data = array('system' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'clear' => array('text' => '数据清理-log'),
                        'commission' => array('text' => '分销关系-log')
                        )
                    )
                );

            if ($_W['isfounder']) {
                $data['system']['child']['clear']['edit'] = '修改';
                $data['system']['child']['clear']['view'] = '公众号选择';
                $data['system']['child']['commission']['edit'] = '修改';
                $data['system']['child']['commission']['view'] = '公众号选择';
                $data['system']['child']['transfer'] = array('text' => '复制转移-log', 'edit' => '修改', 'view' => '公众号选择');
                $data['system']['child']['backup'] = array('text' => '数据下载-log', 'edit' => '修改');
                $data['system']['child']['replacedomain'] = array('text' => '域名转换-log', 'edit' => '修改');
            }
            return $data;
        }
    }
}
