<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/13 下午2:48
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\charts\modules\member\models\YzMember;
use app\common\components\BaseController;

class RelationController extends BaseController
{

    private $memberModel;

    public function __construct()
    {
        parent::__construct();
        $this->memberModel = new YzMember();
    }



    public function index()
    {

        dd($this->getMemberIds());
        return view('charts.member.relation',[])->render();
    }


    public function getMemberIds()
    {
        return $this->memberModel->select('member_id','parent_id','relation')->get();
    }




}
