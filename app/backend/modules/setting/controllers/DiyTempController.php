<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/8
 * Time: 下午4:20
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

class DiyTempController extends BaseController
{
    public function index()
    {
        $kwd = request()->keyword;
        $list = MessageTemp::fetchTempList($kwd)->orderBy('id', 'desc')->paginate(20);
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('setting.diytemp.list', [
            'list' => $list,
            'pager' => $pager,
            'kwd' => $kwd
        ])->render();
    }

    public function add()
    {
        if (request()->temp) {
            $temp_model = new MessageTemp();
            dump($temp_model::handleArray(request()->temp));
            $ret = $temp_model::create($temp_model::handleArray(request()->temp));
            if (!$ret) {
                return $this->message('添加模板失败', Url::absoluteWeb('setting.diy-temp.index'), 'error');
            }
            return $this->message('添加模板成功', Url::absoluteWeb('setting.diy-temp.index'));
        }

        return view('setting.diytemp.detail', [

        ])->render();
    }

    public function edit()
    {
        $temp = '';

        return view('setting.diytemp.detail', [
            'temp' => $temp
        ])->render();
    }

    public function del()
    {
        return $this->message('删除成功', Url::absoluteWeb('setting.diy-temp.index'));
    }

    public function tpl()
    {
        return view('setting.diytemp.tpl.common', [
            'kw' => request()->kw,
            'tpkw' => request()->tpkw,
        ])->render();
    }
}