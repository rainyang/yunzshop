<?php
namespace app\common\helpers;

class WeSession {

    public static $uniacid;

    public static $openid;

    public static $expire;


    public static function start($uniacid, $openid, $expire = 3600) {
        if (empty($GLOBALS['_W']['config']['setting']['memcache']['session']) || empty($GLOBALS['_W']['config']['setting']['memcache']['server'])) {
            WeSession::$uniacid = $uniacid;
            WeSession::$openid = $openid;
            WeSession::$expire = $expire;
            $sess = new WeSession();
            session_set_save_handler(
                array(&$sess, 'open'),
                array(&$sess, 'close'),
                array(&$sess, 'read'),
                array(&$sess, 'write'),
                array(&$sess, 'destroy'),
                array(&$sess, 'gc')
            );
            register_shutdown_function('session_write_close');
        }
        session_start();
    }

    public function open() {
        return true;
    }

    public function close() {
        return true;
    }


    public function read($sessionid) {
        $sql = 'SELECT * FROM ' . tablename('core_sessions') . ' WHERE `sid`=:sessid AND `expiretime`>:time';
        $params = array();
        $params[':sessid'] = $sessionid;
        $params[':time'] = TIMESTAMP;
        $row = pdo_fetch($sql, $params);


        if(is_array($row) && !empty($row['data'])) {
            return $row['data'];
        }

        return false;
    }


    public function write($sessionid, $data) {
        if (!empty($data) && empty($this->chk_member_id_session($data))) {
            $read_data = $this->read($sessionid);

            if (!empty($member_data = $this->chk_member_id_session($read_data))) {
                $data .= $member_data;
            }
        }

        $row = array();
        $row['sid'] = $sessionid;
        $row['uniacid'] = WeSession::$uniacid;
        $row['openid'] = WeSession::$openid;
        $row['data'] = $data;
        $row['expiretime'] = TIMESTAMP + WeSession::$expire;

        return pdo_insert('core_sessions', $row, true) >= 1;
    }


    public function destroy($sessionid) {
        $row = array();
        $row['sid'] = $sessionid;

        return pdo_delete('core_sessions', $row) == 1;
    }


    public function gc($expire) {
        $sql = 'DELETE FROM ' . tablename('core_sessions') . ' WHERE `expiretime`<:expire';

        return pdo_query($sql, array(':expire' => TIMESTAMP)) == 1;
    }

    private function chk_member_id_session($read_data)
    {
        $member_data = '';

        if (!empty($read_data)) {
            preg_match_all('/yunzshop_([\w]+[^|]*|)/', $read_data, $name_matches);
            preg_match_all('/(a:[\w]+[^}]*})/', $read_data, $value_matches);

            if (!empty($name_matches)) {
                foreach ($name_matches[0] as $key => $val) {
                    if ($val == 'yunzshop_member_id') {
                        $member_data = $val . '|' . $value_matches[0][$key];
                    }
                }
            }
        }

        return $member_data;
    }
}