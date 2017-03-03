<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 下午5:40
 */

namespace app\frontend\models;

use app\frontend\modules\member\models\smsSendLimitModel;

class Member extends \app\common\models\Member
{
    /**
     * 验证手机号和密码
     *
     * @return bool
     */
    public static function validate($mobile, $password, $confirm_password)
    {
        $data = array(
            'mobile' => $mobile,
            'password' => $password,
            'confirm_password' => $confirm_password
        );
        $validator = \Validator::make($data, array(
            'mobile' => array('required',
                'digits:11',
                'regex:/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1}))+\d{8})$/'
            ),
            'password' => 'required',
            'confirm_password' => 'same:password'
        ));

        if ($validator->fails()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 短信发送限制
     *
     * 每天最多5条
     */
    public static function smsSendLimit($uniacid, $mobile)
    {
        $curr_time = time();

        $mobile_info = smsSendLimitModel::getDaySmsSendNum($uniacid, $mobile);

        if (!empty($mobile_info)) {
            $update_time = $mobile_info['created_at'];
            $total = $mobile_info['total'];

            if (($update_time <= $curr_time)
                && (data('Ymd', $curr_time) == data('Ymd', $update_time))
                && $total < 5) {

                return true;
            }
        } else {
            $total = 0;
        }

        if ($total < 5) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 更新发送短信条数
     *
     * 每天最多5条
     */
    public static function udpateSmsSendTotal($uniacid, $mobile)
    {
        $curr_time = time();

        $mobile_info = smsSendLimitModel::getMobileInfo($uniacid, $mobile);

        if (!empty($mobile_info)) {
            $update_time = $mobile_info['created_at'];
            $total = $mobile_info['total'];

            if ($update_time <= $curr_time) {
                if (data('Ymd', $curr_time) == data('Ymd', $update_time)) {
                    if ($total <= 4) {
                        ++$total;
                        smsSendLimitModel::updateData(array(
                            'uniacid' => $uniacid,
                            'mobile' => $mobile), array(
                            'total' => $total));
                    }
                } else {
                    smsSendLimitModel::updateData(array(
                        'uniacid' => $uniacid,
                        'mobile' => $mobile), array(
                        'total' => 1,
                        'created_at' => $curr_time));
                }
            }
        } else {
            smsSendLimitModel::insertData(array(
                    'uniaid' => $uniacid,
                    'mobile' => $mobile,
                    'total' => 1,
                    'created_at' => $curr_time)
            );
        }
    }
}