<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\frontend\modules\member\models\Member;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;

class MemberController extends BaseController
{

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::request()->uid;

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                if (!empty($member_info['data'])) {
                    foreach ($member_info['data'] as $key => $item) {
                        if (is_array($item) && !empty($item['yz_member'])) {
                            if (!empty($item['yz_member']['group'])) {
                                foreach ($item['yz_member']['group'] as $k => $v) {
                                    if ($k == 'id') {
                                        $data['group_id'] = $v;
                                    }

                                    $data[$k] = $v;
                                }
                            }

                            if (!empty($item['yz_member']['level'])) {
                                foreach ($item['yz_member']['level'] as $k => $v) {
                                    if ($k == 'id') {
                                        $data['level_id'] = $v;
                                    }

                                    $data[$k] = $v;
                                }
                            }
                        }

                        if (!is_array($item)) {
                            $data[$key] = $item;
                        }
                    }
                } else {
                    return $this->errorJson('用户不存在');
                }

                return $this->successJson('', $data);
            } else {
                return $this->errorJson('用户不存在');
            }

        } else {
            return $this->errorJson('缺少访问参数');
        }

    }
}