<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/29
 * Time: 下午12:23
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('discuzModel')) {
    class discuzModel extends PluginModel
    {
        private static $db;

        /**
         * 创建discuz数据库连接对象
         *
         * @return mixed
         */
        static function getInstance()
        {
            if (self::$db) {
                return self::$db;
            } else {
                include_once UC_ROOT.'./model/base.php';
                $base = new base();

                self::$db = $base->db;

                return self::$db;
            }
        }

        /**
         * discuz表前缀
         *
         * @return string
         */
        private function _dz_db_prefix()
        {
            return substr(UC_DBTABLEPRE,0, strpos(UC_DBTABLEPRE, 'ucenter'));
        }

        /**
         * discuz一键登录
         *
         */
        public function synLogin()
        {
            global $_W;

            //Discuz数据库连接
            $setting = uni_setting($_W['uniacid'], array('uc'));

            if($setting['uc']['status'] == '1') {
                mc_init_uc();

                $exist = $this->hasId();
                if (empty($exist)) {
                    $this->userRegister();
                }

                $this->userLogin();
            } else {
                @message('系统尚未开启UC！', '', 'success');
            }


        }

        /**
         * 会员是否绑定
         *
         */
        public function hasId($uid='')
        {
            global $_W;

            if (empty($uid)) {
                $uid = $this->getUid();
            }

            $id = pdo_fetchcolumn('SELECT id FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));

            return $id;
        }

        /**
         * 会员绑定
         *
         */
        public function userRegister()
        {
            global $_W;

            if (empty($_W['member'])) {
                @message('未关注公众号！', '', 'error');
            }

            $username = $this->getMemberNickName();
            $email    = $_W['member']['email'] ? $_W['member']['email'] : substr(md5(uniqid(mt_rand())), 0, 15) . '@yunzshop.com';
            $password = md5(uniqid(mt_rand()));

            if (strlen($email) > 32) {
                $email = substr(md5($email), 0, 15) . '@yunzshop.com';
            }

            $uid = uc_user_register($username, $password, $email);

            if($uid < 0) {
                if($uid == -1) @message('用户名不合法！', '', 'error');
                elseif ($uid == -2) @message('包含不允许注册的词语！', '', 'error');
                elseif ($uid == -3) @message('用户名已经存在！', '', 'error');
                elseif ($uid == -4) @message('邮箱格式错误！', '', 'error');
                elseif ($uid == -5) @message('邮箱不允许注册！', '', 'error');
                elseif ($uid == -6) @message('邮箱已经被注册！', '', 'error');
            } else {
                $this->RegisterDzMember($uid, $username, $email);
                if($_W['member']['email'] == '') {
                    mc_update($_W['member']['uid'],array('email' => $email));
                }
                pdo_insert('mc_mapping_ucenter', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'centeruid' => $uid));
            }
        }

        /**
         * 同步登录
         *
         */
        public function userLogin($uid = '')
        {
            global $_W;

            $uc = pdo_fetch("SELECT `wx` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));

            $wx = @iunserializer($uc['wx']);

            $bindInfo = $this->getBindInfo($uid);
            $bbsUserInfo = uc_get_user($bindInfo['centeruid'], 1);

            $bbs_uid = $bindInfo['centeruid'];
            $username = $bbsUserInfo[1];

            //用户登陆成功，设置 Cookie，加密直接用 uc_authcode 函数，用户使用自己的函数
            setcookie('bbs_auth', uc_authcode($bbs_uid."\t".$username, 'ENCODE'));

            //生成同步登录的代码
            $ucsynlogin = uc_user_synlogin($bbs_uid);
            echo $ucsynlogin;
            @message('登录成功', $wx['domain'], 'success');
        }

        /**
         * 获取Uc会员信息
         *
         * @return array
         */
        public function getBindInfo($uid = '')
        {
            global $_W;

            if (empty($uid)) {
                $uid = $this->getUid();
            }

            $result = pdo_fetch('SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));

            return $result;
        }

        /**
         * 获取会员nickname
         *
         * @return string
         */
        public function getMemberNickName()
        {
            global $_W;

            $uid = $this->getUid();

            $info = pdo_fetch('SELECT nickname, email FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));

            $nickname = $info['nickname']  . substr($info['email'],0,4);
            return $nickname;
        }

        /**
         * 注册discuz会员
         *
         */
        public function RegisterDzMember($uid, $uname, $email, $pwd = '')
        {
            $prefix = $this->_dz_db_prefix();

            $username = $uname;
            if (empty($pwd)) {
                $password  = md5(time().rand(100000, 999999));
            } else {
                $password  = md5($pwd);
            }

            $email  = $email;
            $ip       = $_SERVER['REMOTE_ADDR'];
            $time   = time();
            $userdata = array('uid' => $uid,
                'username' => $username,
                'password'  => $password,
                'email'       => $email,
                'adminid'   => 0,
                'groupid'   => 10,
                'regdate'   => $time,
                'credits'     => 0,
                'timeoffset' =>9999
            );
            self::getInstance()->query("INSERT INTO " . $prefix . "common_member SET `uid` = '" . $userdata['uid'] . "',
                                             `username` = '" . $userdata['username'] . "', 
                                             `password` = '" . $userdata['password'] ."',
                                             `email`    = '" . $userdata['email'] ."',
                                             `adminid`  = '" . $userdata['adminid'] ."',
                                             `groupid`  = '" . $userdata['groupid'] ."',
                                             `regdate`  = '" . $userdata['regdate'] ."',
                                             `credits`  = '" . $userdata['credits'] ."',
                                             `timeoffset` = '" . $userdata['timeoffset'] ."'");

            $status_data = array('uid' => $uid,
                'regip' => $ip,
                'lastip'  => $ip,
                'lastvisit'       => $time,
                'lastactivity'   => $time,
                'lastpost'   => 0,
                'lastsendmail'   => 0
            );
            self::getInstance()->query("INSERT INTO " . $prefix . "common_member_status SET `uid` = '" . $status_data['uid'] . "', 
                                             `regip` = '" . $status_data['regip'] ."',
                                             `lastip`    = '" . $status_data['lastip'] ."',
                                             `lastvisit`  = '" . $status_data['lastvisit'] ."',
                                             `lastactivity`  = '" . $status_data['lastactivity'] ."',
                                             `lastpost`  = '" . $status_data['lastpost'] ."',
                                             `lastsendmail`  = '" . $status_data['lastsendmail'] ."'");

            self::getInstance()->query("INSERT INTO " . $prefix . "common_member_profile SET `uid` = '" . $uid . "'");
            self::getInstance()->query("INSERT INTO " . $prefix . "common_member_field_forum SET `uid` = '" . $uid . "'");
            self::getInstance()->query("INSERT INTO " . $prefix . "common_member_field_home SET `uid` = '" . $uid . "'");
            self::getInstance()->query("INSERT INTO " . $prefix . "common_member_count SET `uid` = '" . $uid . "'");
        }

        /**
         * 更新discuz会员信息
         *
         * @param array $data
         */
        public function updateUserInfo($uid ,$data = array())
        {
            global $_W;

            if (!$this->isOpenUC() || !$this->chkSynMemberSwitch()) {
                return;
            }


            $exist = pdo_fetch('SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], 'uid' => $uid));

                if (!empty($exist)) {
                    mc_init_uc();

                    $prefix = $this->_dz_db_prefix();

                    $condition = '';
                    foreach ($data as $key => $val) {
                        $condition .= "`{$key}` = '{$val}',";
                    }
                    $condition = rtrim($condition, ',');

                    self::getInstance()->query("UPDATE " . $prefix . "common_member_profile SET {$condition} WHERE `uid`=" . $exist['centeruid']);
                }

        }

        /**
         * 获取会员UID
         *
         * @return integer
         */
        public function getUid()
        {
            $openid = m('user')->getOpenid();
            $uid = mc_openid2uid($openid);

            //load()->model('mc');
            //$uid = mc_openid2uid($openid);

            return $uid;
        }

        /**
         * 同步论坛用户组
         *
         * @param $data
         */
        public function syngroups($data)
        {
            global $_W;

            if (!$this->isOpenUC() || !$this->chkSynGroupSwitch()) {
                return;
            }

            require_once "../framework/model/mc.mod.php";
            mc_init_uc();
            $prefix = $this->_dz_db_prefix();

            foreach ($data as $key => $val) {
                $syn_group = pdo_fetch("SELECT id,groupname,groupid,status FROM " . tablename('sz_yi_member_group') . " WHERE id = '$val'");

                if ($syn_group['status'] == 1) {
                    self::getInstance()->query("UPDATE " . $prefix ."common_usergroup SET `grouptitle`='" . $syn_group['groupname'] .  "'
                          WHERE `groupid`=" . $syn_group['groupid']);
                } else {
                    self::getInstance()->query("INSERT INTO " . $prefix ."common_usergroup SET `grouptitle`='" . $syn_group['groupname'] .  "',
						 `creditshigher`=1, `creditslower`=50, `stars`=1, `allowvisit`=1");

                        $newgid = self::getInstance()->insert_id();

                    pdo_update('sz_yi_member_group', array('groupid'=>$newgid, 'status'=>1), array('id'=>$val));

                    self::getInstance()->query("INSERT INTO " . $prefix . "common_usergroup_field SET `groupid`= " .$newgid.", `allowsearch`=2");

                    self::getInstance()->query("INSERT INTO " . $prefix . "forum_onlinelist SET `groupid`= ".$newgid.", `title`='" . $syn_group['groupname'] ."', `displayorder`='0', `url`=''");

                }
            }
        }

        /**
         * 是否设置UC
         *
         * @return bool
         */
        public function isOpenUC()
        {
            global $_W;

            $setting = uni_setting($_W['uniacid'], array('uc'));

            return $setting['uc']['status'];
        }

        /**
         * 是否开启会员信息同步
         *
         * @return integer
         */
        public function chkSynMemberSwitch()
        {
            global $_W;

            $setting = uni_setting($_W['uniacid'], array('uc'));

            return $setting['uc']['syn_member'];
        }

        /**
         * 是否开启积分同步
         *
         * @return integer
         */
        public function chkSynCreditSwitch()
        {
            global $_W;

            $setting = uni_setting($_W['uniacid'], array('uc'));

            return $setting['uc']['syn_credit'];
        }

        /**
         * 是否开启用户组同步
         *
         * @return integer
         */
        public function chkSynGroupSwitch()
        {
            global $_W;

            $setting = uni_setting($_W['uniacid'], array('uc'));

            return $setting['uc']['syn_group'];
        }

        /**
         * 更新论坛积分&积分记录
         *
         * @param $openid
         * @param $credits
         */
        public function setCredits($openid, $credits, $init_con='0')
        {
            global $_W;

            if (!$this->isOpenUC() || !$this->chkSynCreditSwitch()) {
                return;
            }

            $uid = mc_openid2uid($openid);

            $exist = pdo_fetch('SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], 'uid' => $uid));

            if (!empty($exist)) {
                //更新论坛积分&论坛积分记录
                if (empty($init_con)) {
                    mc_init_uc();
                }
                $prefix = $this->_dz_db_prefix();

                $result = self::getInstance()->fetch_first("SELECT extcredits4 FROM " . $prefix . "common_member_count  WHERE uid =" . $exist['centeruid']);

                $new_credit = $credits + $result['extcredits4'];

                if ($new_credit <= 0) {
                    $new_credit = 0;
                }

                self::getInstance()->query("UPDATE " . $prefix . "common_member_count SET extcredits4 = {$new_credit} WHERE uid =" . $exist['centeruid']);

                if ($credits >= 0) {
                    $int_credits = '+' . $credits;
                } else {
                    $int_credits = $credits;
                }
                self::getInstance()->query("UPDATE " . $prefix ."common_member SET `credits`= `credits`" . $int_credits . " WHERE `uid`=" . $exist['centeruid']);

                self::getInstance()->query("INSERT INTO " . $prefix . "common_credit_log SET `uid`=". $exist['centeruid'] . ", `operation`='ECU', `relatedid`=" . $exist['centeruid'] . ",
                      `dateline`='" . time() . "', `extcredits4`=" . $credits );
            }
        }

        /**
         * pc端微信登录获取用户信息
         *
         * @param $appid
         * @param $appsecret
         * @return array|mixed|stdClass
         */
        function userinfo($appid, $appsecret)
        {
            global $_W;

            if ($_GET['state'] != $_SESSION["wx_state"]) {
                exit("5001");
            }

            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $_GET['code'] . '&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $json = curl_exec($ch);
            curl_close($ch);
            $arr = json_decode($json, 1);

            $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $arr['access_token'] . '&openid=' . $arr['openid'] . '&lang=zh_CN';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $json = curl_exec($ch);
            curl_close($ch);
            $arr = json_decode($json, 1);

            return $arr;
        }

        /**
         * pc微信扫码注册
         *
         */
        public function userScanRegister($uuid, $uname, $email, $pwd='')
        {
            global $_W;

            $username = $uname;
            $email    = $email;
            $password = $pwd;

            $uid = uc_user_register($username, $password, $email);

            if($uid < 0) {
                if($uid == -1) @message('用户名不合法！', '', 'error');
                elseif ($uid == -2) @message('包含不允许注册的词语！', '', 'error');
                elseif ($uid == -3) @message('用户名已经存在！', '', 'error');
                elseif ($uid == -4) @message('邮箱格式错误！', '', 'error');
                elseif ($uid == -5) @message('邮箱不允许注册！', '', 'error');
                elseif ($uid == -6) @message('邮箱已经被注册！', '', 'error');
            } else {
                $this->RegisterDzMember($uid, $username, $email);

                pdo_insert('mc_mapping_ucenter', array('uniacid' => $_W['uniacid'], 'uid' => $uuid, 'centeruid' => $uid));
            }
        }

        /**
         * 获取绑定前论坛会员积分
         *
         * @param $ucid
         * @return mixed
         */
        public function getUCCredits($ucid)
        {
            $prefix = $this->_dz_db_prefix();

            $result = self::getInstance()->fetch_first("SELECT `credits` FROM " . $prefix . "common_member  WHERE uid =" . $ucid);

            return $result['credits'];
        }

        /**
        * 同步论坛积分到商城
        *
        */
        public function setShopCredit($ucid, $credit, $log = array())
        {
            global $_W;

            $exist = pdo_fetch("SELECT * FROM " .tablename('mc_mapping_ucenter') . " WHERE `uniacid` = " . $_W['uniacid'] . " AND `centeruid` =" . $ucid);

            if (!empty($exist)) {
                $value     = pdo_fetchcolumn("SELECT credit1 FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                    ':uid' => $exist['uid']
                ));

                $newcredit = $credit + $value;
                if ($newcredit <= 0) {
                    $newcredit = 0;
                }

                pdo_update('mc_members', array(
                    'credit1' => $newcredit
                ), array(
                    'uid' => $exist['uid']
                ));
                if (empty($log) || !is_array($log)) {
                    $log = array(
                        $exist['uid'],
                        '论坛同步积分'
                    );
                }
                $data = array(
                    'uid' => $exist['uid'],
                    'credittype' => 'credit1',
                    'uniacid' => $_W['uniacid'],
                    'num' => $credit,
                    'createtime' => TIMESTAMP,
                    'operator' => intval($log[0]),
                    'remark' => $log[1]
                );
                pdo_insert('mc_credits_record', $data);
            }
        }

        /**
        * 是否有绑定数据
        *
        */
        public function isExist($uid)
        {
            global $_W;

            $exist = pdo_fetch('SELECT * FROM ' . tablename('mc_mapping_ucenter') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));

            return $exist;
        }

        /**
        * 删除论坛里的商城积分
        *
        */
        public function delShopCreditFromUC($exist)
        {
        
            $prefix = $this->_dz_db_prefix();

            $result = self::getInstance()->fetch_first("SELECT extcredits4 FROM " . $prefix . "common_member_count  WHERE uid =" . $exist['centeruid']);

               
            self::getInstance()->query("UPDATE " . $prefix . "common_member_count SET extcredits4 = 0 WHERE uid =" . $exist['centeruid']);

            self::getInstance()->query("UPDATE " . $prefix . "common_member SET credits = credits-{$result['extcredits4']} WHERE uid =" . $exist['centeruid']);
            
        }

        /**
        * 删除商城里的uc积分
        *
        */
        public function delUCCreditFromShop($uid, $credit)
        {
            global $_W;

             pdo_update('mc_members', array(
                    'credit1' => $credit
                ), array(
                    'uid' => $uid
                )
            );

             if (empty($log) || !is_array($log)) {
                    $log = array(
                        $uid,
                        '删除论坛同步积分'
                    );
                }
                $data = array(
                    'uid' => $uid,
                    'credittype' => 'credit1',
                    'uniacid' => $_W['uniacid'],
                    'num' => $credit,
                    'createtime' => TIMESTAMP,
                    'operator' => intval($log[0]),
                    'remark' => $log[1]
                );
                pdo_insert('mc_credits_record', $data);


        }

        /**
        * 获取论坛里商城积分
        *
        */
        public function getShopCreditFromUC($exist)
        {
            $prefix = $this->_dz_db_prefix();

            $result = self::getInstance()->fetch_first("SELECT extcredits4 FROM " . $prefix . "common_member_count  WHERE uid =" . $exist['centeruid']);

            return $result['extcredits4'];

        }
    }
}
