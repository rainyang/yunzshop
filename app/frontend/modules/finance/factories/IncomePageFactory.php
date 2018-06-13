<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午2:05
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\finance\factories;


use app\backend\modules\member\models\MemberRelation;
use app\common\exceptions\AppException;
use app\common\models\Income;
use app\frontend\modules\finance\interfaces\IIncomePage;
use app\frontend\modules\member\models\MemberModel;

class IncomePageFactory
{
    /**
     * @var IIncomePage
     */
    private $_income;


    /**
     * 会员是否是推客
     *
     * @var bool
     */
    private $is_agent;


    /**
     * 是否开启关系链
     *
     * @var bool
     */
    private $is_relation;



    private $lang_set;


    public function __construct(IIncomePage $income, $lang_set, $is_relation = false, $is_agent = false)
    {
        $this->_income = $income;
        $this->is_agent = $is_agent;
        $this->is_relation = $is_relation;
        $this->lang_set = $lang_set;
    }


    /**
     * 收入模型是否显示
     *
     * @return bool
     */
    public function isShow()
    {
        return $this->_income->isShow();
    }


    /**
     * 是否拥有收入模型使用权限
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->_income->isAvailable();
    }


    /**
     * 是否需要验证 是否开启关系链
     *
     * @return bool
     */
    public function validatorIsRelation()
    {
        return $this->_income->needIsRelation();
    }


    /**
     * 是否需要验证 是否是推客
     *
     * @return bool
     */
    public function validatorIsAgent()
    {
        return $this->_income->needIsAgent();
    }


    /**
     * 获取收入模型数据
     *
     * @return array
     * @throws AppException
     */
    public function getIncomeData()
    {
        if (!$this->isShow()) {
            throw new AppException('IncomeFactory' . $this->_income->getTitle() . 'no use');
        }

        return [
            'url' => $this->_income->getAppUrl(),
            'icon' => $this->_income->getIcon(),
            'mark' => $this->getMark(),
            'title' => $this->getTitle(),
            'level' => $this->_income->getLevel(),
            'value' => $this->getValue(),
            'is_agent' => $this->isAgent(),
            'is_relation' => $this->isRelation(),
        ];
    }


    private function getMark()
    {
        return $this->_income->getMark();
    }


    private function getTitle()
    {
        $mark = $this->_income->getMark();

        if (isset($this->lang_set[$mark]['title']) && !empty($this->lang_set[$mark]['title'])) {
            return $this->lang_set[$mark]['title'];
        }
        return $this->_income->getTitle();
    }


    /**
     * 收入模型累计收入值
     *
     * @return mixed
     */
    private function getValue()
    {
        $type = $this->_income->getTypeValue();
        $member_id = \YunShop::app()->getMemberId();

        return Income::where('incometable_type', $type)->whereStatus(0)->whereMember_id($member_id)->sum('amount');
    }


    /**
     * 如果需要验证关系链，返回关系链状态，默认返回 true
     * @return bool
     */
    private function isRelation()
    {
        //已经获得权限不需要验证关系链
        if ($this->isAvailable()) {
            return true;
        }
        if ($this->_income->needIsRelation()) {
            return $this->is_relation;
        }
        return true;
    }


    /**
     * 如果需要验证是否是推客，返回推客状态，默认返回 true
     *
     * @return bool
     */
    private function isAgent()
    {
        //已经获得权限不需要验证是否是推客
        if ($this->isAvailable()) {
            return true;
        }
        if ($this->_income->needIsRelation()) {
            return $this->is_agent;
        }
        return true;
    }



}
