<?php
/*=============================================================================
#     FileName: core.php
#         Desc: 商城核心文件
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:09:18
#      History:
=============================================================================*/

if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Core extends WeModuleSite
{

    public $footer = array();
    public $header = null;
    public $yzShopSet = array();
    public $yzImages = array();
    public function __construct()
    {
        global $_W, $_GPC;
        m('common')->checkClose();
        if (empty($_W['uniacid'])) {
            if (!empty($_W['uid'])) {
                $_W['uniacid'] = pdo_fetchcolumn("select uniacid from " . tablename('sz_yi_perm_user') . " where uid={$_W['uid']}");
                $_W['issupplier'] = 1;
            }
        }
        if (is_weixin()) {
            m('member')->checkMember();

            //discuz论坛一键登录
            if (!empty($_GPC['refer']) && $_GPC['refer'] == 'bbs' && p('discuz')) {
                //p('discuz')->synLogin();
                header("Location:/app/index.php?i=" . $_W['uniacid'] . "&c=entry&method=synlogin&p=discuz&m=sz_yi&do=plugin");
            }
        } else {
            //APP 分销分享地址-用户注册
            $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
            $set     = unserialize($setdata['sets']);

            if ($set['app']['base']['share']['switch'] == 1 && isset($_GPC['access']) && $_GPC['access'] == 'app') {
                header("Location:/app/index.php?i=" . $_W['uniacid'] . "&c=entry&p=register&do=member&m=sz_yi&from=app&mid=".$_GPC['mid']);
            }

            $noLoginList = array('poster', 'postera');
            //微信回调通知要加入开放权限
            if (p('commission') && (!in_array($_GPC['p'], $noLoginList)) && !strpos($_SERVER['SCRIPT_NAME'], 'notify')) {
                if (strexists($_SERVER['REQUEST_URI'], '/web/')) {
                    return;
                }
                p('commission')->checkAgent();
            }
        }
        $this->yzShopSet = m('common')->getSysset('shop');
        $this->yzImages = set_medias(m('common')->getSysset('shop'), array('logo', 'img', 'pclogo', 'reglogo'));

        if (is_app()) {
            /**
             * 设置app端使用模板文件夹
             */
            $_W['template'] = 'app';
        }

        
    }

    public function sendSms($mobile, $code, $templateType = 'reg')
    {
        $set = m('common')->getSysset();
        if ($set['sms']['type'] == 1) {
            return send_sms($set['sms']['account'], $set['sms']['password'], $mobile, $code);
        } else {
            return send_sms_alidayu($mobile, $code, $templateType);
        }
    }
    public function runTasks()
    {
        global $_W;
        load()->func('communication');
        $lasttime = strtotime(m('cache')->getString('receive', 'global'));
        $interval = intval(m('cache')->getString('receive_time', 'global'));
        if (empty($interval)) {
            $interval = 60;
        }
        $interval *= 60;
        $current = time();
        if ($lasttime + $interval <= $current) {
            m('cache')->set('receive', date('Y-m-d H:i:s', $current), 'global');
            $reveive_url = $this->createMobileUrl('order/receive');
            ihttp_request($reveive_url, null, null, 1);
        }
        $lasttime = strtotime(m('cache')->getString('closeorder', 'global'));
        $interval = intval(m('cache')->getString('closeorder_time', 'global'));
        if (empty($interval)) {
            $interval = 60;
        }
        $interval *= 60;
        $current = time();
        if ($lasttime + $interval <= $current) {
            m('cache')->set('closeorder', date('Y-m-d H:i:s', $current), 'global');
            $close_url = $this->createMobileUrl('order/close');
            ihttp_request($close_url, null, null, 1);
        }

        if (p('coupon')) {
            $couponbacktime = strtotime(m('cache')->getString('couponbacktime', 'global'));
            $coupon_set = p('coupon')->getSet();
            $backruntime = intval($coupon_set['backruntime']);
            if (empty($backruntime)) {
                $backruntime = 60;
            }
            
            $backruntime *= 60;
            $time = time();
            if ($couponbacktime + $backruntime <= $time) {
                m('cache')->set('couponbacktime', date('Y-m-d H:i:s', $time), 'global');
                $back_url = $this->createPluginMobileUrl('coupon/back');
                ihttp_request($back_url, null, null, 1);
            }
        }
        exit('run finished.');
    }

    public function setHeader()
    {
        global $_W, $_GPC;
        $openid   = m('user')->getOpenid();
        $followed = m('user')->followed($openid);
        $mid      = intval($_GPC['mid']);
        $memberid = m('member')->getMid();
        $this->setFooter();

        @session_start();
        if (!$followed && $memberid != $mid && isMobile()) {
            $set          = m('common')->getSysset();
            $this->header = array(
                'url' => $set['share']['followurl']
            );
            $friend       = false;
            if (!empty($mid)) {
                if (!empty($_SESSION[SZ_YI_PREFIX . '_shareid']) && $_SESSION[SZ_YI_PREFIX . '_shareid'] == $mid) {
                    $mid = $_SESSION[SZ_YI_PREFIX . '_shareid'];
                }
                $member = m('member')->getMember($mid);
                if (!empty($member)) {
                    $_SESSION[SZ_YI_PREFIX . '_shareid'] = $mid;
                    $friend                                  = true;
                    $this->header['icon']                    = $member['avatar'];
                    $this->header['text']                    = '来自好友 <span>' . $member['nickname'] . '</span> 的推荐';
                }
            }
            if (!$friend) {
                $this->header['icon'] = tomedia($set['shop']['logo']);
                $this->header['text'] = '欢迎进入 <span>' . $set['shop']['name'] . '</span>';
            }
        }
    }
    public function setFooter()
    {
        global $_W, $_GPC;
        $p = strtolower(trim($_GPC['p']));
        $method = strtolower(trim($_GPC['method']));
        if (strexists($p, 'poster') && $method == 'build') {
            return;
        }
        if (strexists($p, 'designer') && ($method == 'index' || empty($method)) && $_GPC['preview'] == 1) {
            return;
        }
        $openid = m('user')->getOpenid();
        $member  = m('member')->getMember($openid);
        if (!empty($member['isblack'])) {
            if ($_GPC['op'] != 'black') {
                header('Location: '.$this->createMobileUrl('member/login', array('op' => 'black')));
            }
        }
        
        if (is_weixin()) {
            //url未有mid重新跳转当前页并添加,无分享接口用户使用
            if(empty($_GPC['mid']) && !$_W['isajax']){
                if($member['isagent'] == 1 && $member['status'] == 1){
                    header("Location: ". $_W['siteroot'] . 'app/index.php?'.$_SERVER['QUERY_STRING']."&mid=".$member['id']);
                    exit();
                }
            }
            //是否强制绑定手机号,只针对微信端
            if (!empty($this->yzShopSet['isbindmobile'])) {
                if (empty($member) || $member['mobile'] == ""){
                    if ($_GPC['p'] != 'bindmobile' && $_GPC['p'] != 'sendcode') {
                        $bindmobileurl = $this->createMobileUrl('member/bindmobile');
                        YZredirect($bindmobileurl);
                        exit();
                    }
                }
            }
        }
        $designer = p('designer');
        if ($designer && $_GPC['p'] != 'designer') {
            $menu = $designer->getDefaultMenu();
            if (!empty($menu)) {
                if (!is_weixin_show()) {
                    $newmenu = json_decode($menu['menus'], true);
                    foreach ($newmenu as &$val) {
                        if (!empty($val['url'])) {
                            if (strpos($val['url'], 'commission') !== false or strpos($val['url'], 'bonus') !== false) {
                                $val['url'] = $this->createMobileUrl('member/bindapp');
                                $val['title'] = 'APP下载';
                            }
                        }
                        if (!empty($val['subMenus'])) {
                            foreach ($val['subMenus'] as &$sv) {
                                if (strpos($sv['url'], 'commission') !== false or (strpos($sv['url'], 'bonus') !== false)) {
                                    $sv['url'] = $this->createMobileUrl('member/bindapp');
                                    $sv['title'] = 'APP下载';
                                }
                            }
                        }
                    }
                    $menu['menus'] = json_encode($newmenu);
                }
                $this->footer['diymenu']   = true;
                $this->footer['diymenus']  = $menu['menus'];
                $this->footer['diyparams'] = $menu['params'];
                return;
            }
        }
        $mid                        = intval($_GPC['mid']);
        $this->footer['first']      = array(
            'text' => '首页',
            'ico' => 'home',
            'url' => $this->createMobileUrl('shop')
        );
        $this->footer['second']     = array(
            'text' => '分类',
            'ico' => 'list',
            'url' => $this->createMobileUrl('shop/category')
        );

        $this->footer['commission'] = false;


        if (p('commission')) {
            $set = p('commission')->getSet();
            if (empty($set['level'])) {
                return;
            }
            $isagent = $member['isagent'] == 1 && $member['status'] == 1;
            if ($_GPC['do'] == 'plugin') {
                $this->footer['first'] = array(
                    'text' => empty($set['closemyshop']) ? $set['texts']['shop'] : '首页',
                    'ico' => 'home',
                    'url' => empty($set['closemyshop']) ? $this->createPluginMobileUrl('commission/myshop', array(
                        'mid' => $member['id']
                    )) : $this->createMobileUrl('shop')
                );
                if ($_GPC['method'] == '') {
                    $this->footer['first']['text'] = empty($set['closemyshop']) ? $set['texts']['myshop'] : '首页';
                }
                if (empty($member['agentblack'])) {
                    $this->footer['commission'] = array(
                        'text' => $set['texts']['center'],
                        'ico' => 'sitemap',
                        'url' => $this->createPluginMobileUrl('commission')
                    );
                    if (!is_weixin_show()) {
                        $this->footer['commission'] = array(
                            'text' => 'APP下载',
                            'ico' => 'sitemap',
                            'url' => $this->createMobileUrl('member/bindapp')
                        );
                    }
                }
            } else {
                if (empty($member['agentblack'])) {
                    if (!$isagent) {
                        $this->footer['commission'] = array(
                            'text' => $set['texts']['become'],
                            'ico' => 'sitemap',
                            'url' => $this->createPluginMobileUrl('commission/register')
                        );
                        if (!is_weixin_show()) {
                            $this->footer['commission'] = array(
                                'text' => 'APP下载',
                                'ico' => 'sitemap',
                                'url' => $this->createMobileUrl('member/bindapp')
                            );
                        }
                    } else {
                        $this->footer['commission'] = array(
                            'text' => empty($set['closemyshop']) ? $set['texts']['shop'] : $set['texts']['center'],
                            'ico' => empty($set['closemyshop']) ? 'heart' : 'sitemap',
                            'url' => empty($set['closemyshop']) ? $this->createPluginMobileUrl('commission/myshop', array(
                                'mid' => $member['id']
                            )) : $this->createPluginMobileUrl('commission')
                        );
                        if (!is_weixin_show()) {
                            $this->footer['commission'] = array(
                                'text' => 'APP下载',
                                'ico' => empty($set['closemyshop']) ? 'heart' : 'sitemap',
                                'url' => $this->createMobileUrl('member/bindapp')
                            );
                        }
                    }
                }
            }
        }

        if (strstr($_SERVER['REQUEST_URI'], 'app')) {
            if (!isMobile()) {
                if ($this->yzShopSet['ispc']==0) {
                    //message('抱歉，PC版暂时关闭，请用微信打开!','','error');
                }
            }
        }
        
    }
    public function createMobileUrl($do, $query = array(), $noYZredirect = true)
    {
        global $_W, $_GPC;
        $do = explode('/', $do);
        if (isset($do[1])) {
            $query = array_merge(array(
                'p' => $do[1]
            ), $query);
        }
        if (empty($query['mid'])) {
            $mid = intval($_GPC['mid']);
            if (!empty($mid)) {
                $query['mid'] = $mid;
            }
        }

        if (empty($query['m'])) {
            $this->modulename = empty($_GPC['m'])?:'sz_yi';
        }
        return $_W['siteroot'] . 'app/' . substr(parent::createMobileUrl($do[0], $query, true), 2);
    }
    public function createWebUrl($do, $query = array())
    {
        global $_W;
        $do = explode('/', $do);
        if (count($do) > 1 && isset($do[1])) {
            $query = array_merge(array(
                'p' => $do[1]
            ), $query);
        }
        return $_W['siteroot'] . 'web/' . substr(parent::createWebUrl($do[0], $query, true), 2);
    }
    public function createPluginMobileUrl($do, $query = array())
    {
        global $_W, $_GPC;
        $do         = explode('/', $do);
        $query      = array_merge(array(
            'p' => $do[0]
        ), $query);
        $query['m'] = 'sz_yi';
        if (isset($do[1])) {
            $query = array_merge(array(
                'method' => $do[1]
            ), $query);
        }
        if (isset($do[2])) {
            $query = array_merge(array(
                'op' => $do[2]
            ), $query);
        }
        if (empty($query['mid'])) {
            $mid = intval($_GPC['mid']);
            if (!empty($mid)) {
                $query['mid'] = $mid;
            }
        }
        return $_W['siteroot'] . 'app/' . substr(parent::createMobileUrl('plugin', $query, true), 2);
    }
    public function createPluginWebUrl($do, $query = array())
    {
        global $_W;
        $do    = explode('/', $do);
        $query = array_merge(array(
            'p' => $do[0]
        ), $query);
        if (isset($do[1])) {
            $query = array_merge(array(
                'method' => $do[1]
            ), $query);
        }
        if (isset($do[2])) {
            $query = array_merge(array(
                'op' => $do[2]
            ), $query);
        }
        return $_W['siteroot'] . 'web/' . substr(parent::createWebUrl('plugin', $query, true), 2);
    }
    public function _exec($do, $default = '', $web = true)
    {
        global $_GPC;
        $do = strtolower(substr($do, $web ? 5 : 8));
        $p  = trim($_GPC['p']);
        empty($p) && $p = $default;
        if ($web) {
            $file = IA_ROOT . "/addons/sz_yi/core/web/" . $do . "/" . $p . ".php";
        } else {
            $this->setFooter();
            $file = IA_ROOT . "/addons/sz_yi/core/mobile/" . $do . "/" . $p . ".php";
        }
        if (!is_file($file)) {
            message("未找到 控制器文件 {$do}::{$p} : {$file}");
        }
        return include $file;
    }

    public function _execFront($do, $default = '', $web = true)
    {
        global $_W, $_GPC;
        //todo, 需要加入微信权限认证
        define('IN_SYS', true);
        $_W['templateType'] = 'web';
        $do = strtolower(substr($do, 5));
        $p  = trim($_GPC['p']);
        empty($p) && $p = $default;
        $file = IA_ROOT . "/addons/sz_yi/core/web/" . $do . "/" . $p . ".php";
        if (!is_file($file)) {
            message("未找到 控制器文件 {$do}::{$p} : {$file}");
        }
        include $file;
        exit;
    }

    public function template($filename, $type = TEMPLATE_INCLUDEPATH)
    {
        global $_W;
        if (is_app()) {
            m('cache')->set('app_template_shop', $_W['template']);
        }

        //print_r($_SERVER);exit;

        $tmplateType = (isMobile()) ? 'mobile' : 'pc';
        $set = m('common')->getSysset('shop');
        if (strstr($_SERVER['REQUEST_URI'], 'app')) {
            if (!isMobile()) {
                if ($set['ispc']==0) {
                    $tmplateType = 'mobile';
                    //message('抱歉，PC版暂时关闭，请用微信打开!','','error');
                }
            }
        }

        $name = strtolower($this->modulename);
        if (defined('IN_SYS')) {
            $source  = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$filename}.html";
            }
            if (!is_file($source)) {
                $explode = explode('/', $filename);
                $temp    = array_slice($explode, 1);
                $source  = IA_ROOT . "/addons/{$name}/plugin/" . $explode[0] . "/template/" . implode('/', $temp) . ".html";
            }
        } else {
            if (is_app()) {
                $template = m('cache')->getString('app_template_shop');
            } else {
                if (!isMobile() && $set['ispc']) {
                    $template = m('cache')->getString('template_shop_pc');
                } else {
                    $template = m('cache')->getString('template_shop');
                }
            }

            if (empty($template)) {
                $template = "default";
            }
            if (!is_dir(IA_ROOT . '/addons/sz_yi/template/'.$tmplateType.'/' . $template)) {
                $template = "default";
            }

            $compile = IA_ROOT . "/data/tpl/app/sz_yi/{$template}/{$tmplateType}/{$filename}.tpl.php";
            $source  = IA_ROOT . "/addons/{$name}/template/{$tmplateType}/{$template}/{$filename}.html";
//echo $source;exit;
            if (!is_file($source)) {
                $source = IA_ROOT . "/addons/{$name}/template/{$tmplateType}/default/{$filename}.html";
            }
            if (!is_file($source)) {
                $names      = explode('/', $filename);
                $pluginname = $names[0];
                if ($pluginname == "designer") {
                    $ptemplate = $template;
                } else {
                    $ptemplate  = m('cache')->getString('template_' . $pluginname);
                }
                if (empty($ptemplate)) {
                    $ptemplate = "default";
                }
                if (!is_dir(IA_ROOT . '/addons/sz_yi/plugin/' . $pluginname . "/template/{$tmplateType}/" . $ptemplate)) {
                    $ptemplate = "default";
                }
                $pfilename = $names[1];
                $source    = IA_ROOT . "/addons/sz_yi/plugin/" . $pluginname . "/template/{$tmplateType}/" . $ptemplate . "/{$pfilename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/default/{$filename}.html";
            }
        }
        if (!is_file($source)) {
            exit("Error: template source '{$filename}' is not exist!");
        }
        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            shop_template_compile($source, $compile, true);
        }
        return $compile;
    }
    public function getUrl()
    {
        if (p('commission')) {
            $set = p('commission')->getSet();
            if (!empty($set['level'])) {
                return $this->createPluginMobileUrl('commission/myshop');
            }
        }
        return $this->createMobileUrl('shop');
    }

    /*private function executeTasks()
    {
        global $_W;
        load()->func('communication');
        $lasttime = strtotime(m('cache')->getString('receive', 'global'));
        $interval = intval(m('cache')->getString('receive_time', 'global'));
        if (empty($interval)) {
            $interval = 60;
        }
        $interval *= 60;
        $current = time();
        if ($lasttime + $interval <= $current) {
            m('cache')->set('receive', date('Y-m-d H:i:s', $current), 'global');
            ihttp_request($_W['siteroot'] . "addons/sz_yi/core/mobile/order/receive.php", null, null, 1);
        }
        $lasttime = strtotime(m('cache')->getString('closeorder', 'global'));
        $interval = intval(m('cache')->getString('closeorder_time', 'global'));
        if (empty($interval)) {
            $interval = 60;
        }
        $interval *= 60;
        $current = time();
        if ($lasttime + $interval <= $current) {
            m('cache')->set('closeorder', date('Y-m-d H:i:s', $current), 'global');
            ihttp_request($_W['siteroot'] . "addons/sz_yi/core/mobile/order/close.php", null, null, 1);
        }
    }*/
}
