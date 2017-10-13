<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\common\components\BaseController;

class RelationController extends BaseController
{
    public function index()
    {

        return view('charts.member.relation',[])->render();
    }

}
