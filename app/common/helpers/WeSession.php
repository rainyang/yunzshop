<?php
namespace app\common\helpers;


use Illuminate\Support\Facades\DB;

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
        $data = DB::table('core_sessions')->whereSid($sessionid)->whereExpiretime(TIMESTAMP)->value('data');

        if(empty($data)) {
            return $data;
        }
        return false;
    }


    public function write($sessionid, $data) {
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
        return DB::table('core_sessions')->where('expiretime','<',TIMESTAMP)->delete() == 1;
    }
}