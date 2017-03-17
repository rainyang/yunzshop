<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\user\UniAccountUser;
use app\common\models\user\User;
use app\common\models\user\UserProfile;
use app\common\models\user\YzRole;

class UserController extends BaseController
{
    public function index()
    {

        $pageSize = 2;

        $userList = UniAccountUser::getUserList($pageSize);
        //dd($userList);
        return view('user.user.user',[
            'pager'     => '',
            'roleList'  => ''
        ])->render();
    }

    public function store()
    {
        $requestUser = \YunShop::request()->user;

        if ($requestUser) {
            //dd($requestUser);
            $userModel = new User();

            $userData = $this->addedUserData($requestUser);
            $userData = $userData + $userModel->attributes;

            $userModel->setRawAttributes($userData);
            $userModel->widgets = \YunShop::request()->widgets;

            $validator = User::validator($userModel->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $userModel->password = $this->password($userModel->password, $userModel->salt);
                if ($userModel->save()) {
                    dd(12);
                    return $this->message('添加操作员成功.', Url::absoluteWeb('user.user.index'));
                }
            }

        }
        $permissions = \Config::get('menu');
        $roleList = YzRole::getRoleListToUser();
        //dd($roleList);

        return view('user.user.form',[
            'user'=>array( 'status' => 1, 'id' => ''),
            'roleList' => $roleList,
            'permissions'=>$permissions,
            'userPermissons'=>[],
        ])->render();
    }

    /**
     * 附加的用户数据
     * @param string $data 需要储存的数据
     * @return string
     */
    private function addedUserData(array $data = [])
    {
        $data['joindate']    = $data['lastvisit'] = $data['starttime'] =time();
        $data['lastip']      = CLIENT_IP;
        $data['joinip']      = CLIENT_IP;
        $data['salt']        = $this->randNum(8);

        return $data;
    }

    /**
     * 计算用户密码
     * @param string $passwordinput 输入字符串
     * @param string $salt 附加字符串
     * @return string
     */
    private function password($passwordinput, $salt) {
        $authkey = \YunShop::app()->config['setting']['authkey'];
        $passwordinput = "{$passwordinput}-{$salt}-{$authkey}";
        return sha1($passwordinput);
    }
    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    private function randNum($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

}