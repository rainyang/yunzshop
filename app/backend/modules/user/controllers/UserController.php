<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;
use app\common\models\user\User;
use app\common\models\user\UserProfile;
use app\common\models\user\YzRole;

class UserController extends BaseController
{
    public function index()
    {
        dd(\YunShop::app()->uniacid);
        return view('user.user.user',[
            'pager'     => '',
            'roleList'  => ''
        ])->render();
    }

    public function store()
    {

        $requestUser = \YunShop::request()->user;
        if ($requestUser) {
            //验证用户名是否存在
            if (empty($requestUser['username']) || User::checkUserName($requestUser['username'])) {
                return $this->message('非法用户名或用户名已存在，请重新添加！', '',  'error');
            }
            //密码不能为空
            if (empty($requestUser['password'])) {
                return $this->message('密码不能为空！', '',  'error');
            }

            $userModel = new User();
            //附值数据
            $userData = $this->addedUserData($requestUser);
            $userData = $userData + $userModel->attributes;
            $userModel->setRawAttributes($userData);
            //验证数据
            $validator = User::validator($userModel->getAttributes());
            if ($validator->fails()) {
                return $this->message($validator->message(), '', 'error');
            }else{

            }
            //添加操作员
            if (!$userModel->save()) {
                return $this->message('操作员用户信息写入失败，请重试！', '', 'error');
            }
        //user_profile
            $requestProfile = \YunShop::request()->profile;
            if ($requestProfile) {

                $profileModel = new UserProfile();
                //附值数据
                $requestProfile = $requestProfile + $profileModel->attributes;
                $profileModel->setRawAttributes($requestProfile);
                $profileModel->uid = $userModel->id;
                $profileModel->createtime = time();
                //验证数据
                $validator = UserProfile::validator($profileModel->getAttributes());
                if ($validator->fails()) {
                    return $this->message($validator->message(), '', 'error');
                }
                //添加操作员信息
                if (!$profileModel->save()) {
                    return $this->message('操作员资料信息写入失败，请重试！', '', 'error');
                }
            }
                //uni_account_user
                //user_permission
                //yz_permission
            return $this->message('操作员添加成功。');

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
        $data['password']    = $this->password($data['password'], $data['salt']);
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