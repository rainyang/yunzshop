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
use app\backend\modules\setting\controllers\SmallProgramDataController;
use app\backend\modules\setting\controllers\DiyTempController;
use app\common\models\notice\MinAppTemplateMessage;
use app\common\services\notice\SmallProgramNotice;
class SmallProgramController extends BaseController
{
    private $temp_model;

    public function index()
    {
        $kwd = request()->keyword;
        $list = MinAppTemplateMessage::getList();
        return view('setting.small-program.list', [
            'list' => $list->toArray(),
//            'pager' => $pager,
            'kwd' => $kwd,
            'url'=>'setting.small-program.save'
        ])->render();
    }

    public function add()
    {
        $small = new SmallProgramNotice();
        $list = $small->getExistTemplateList();
        if ($list['errcode'] != 0 || !isset($list['errcode'])){
            return $this->message('获取模板失败'.$list, Url::absoluteWeb('setting.small-program.index'), 'error');
        }
        return view('setting.small-program.detail', [
                'list'=>$list['list'],
                'url'=>'setting.small-program.save'
            ])->render();
    }
    public function addTmp()
    {
        if (!request()->templateidshort) {
            return $this->errorJson('请填写模板编码');
        }
        $ret = $this->WechatApiModel->getTmpByTemplateIdShort(request()->templateidshort);
        if ($ret['status'] == 0) {
            return $this->errorJson($ret['msg']);
        } else {
            return $this->successJson($ret['msg'], []);
        }
    }
    public function getTemplateKey(){
        if (isset(request()->key_val)){
            $ret = $this->save(request()->all());
            if (!$ret){
                return $this->message('添加模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
            }
            return $this->message('添加模板成功', Url::absoluteWeb('setting.small-program.index'));
        }
        $page = request()->page;
        if (isset(request()->id)){
            $small = new SmallProgramNotice();
            $key_list = $small->getTemplateKey(request()->id);
            if ($key_list['errcode'] != 0 || !isset($key_list['errcode'])){
                return $this->message('获取模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
            }
            $keyWord = $key_list['keyword_list'];
        }
        return view('setting.small-program.detail', [
            'keyWord'=>$keyWord,
            'list'=>$small->getAllTemplateList($page)['list'],
            'page'=>$page,
            'title'=>request()->title,
            'url'=>'setting.small-program.save'
        ])->render();
   }
   public function save($list){
       $strip = 0;
        $date['data'] = [];
        foreach ($list['key_val'] as $value){
            $key_list[] = explode(":",$value)[0];
        }
        $small = new SmallProgramNotice();
        $template_date = $small->getAddTemplate($list['id'],$key_list);
       if ($template_date['errcode'] != 0 || !isset($template_date['errcode'])){
           return $this->message('添加模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
       }
        if ($template_date['errcode'] == 0){
            $ret = MinAppTemplateMessage::create([
                'uniacid' => \YunShop::app()->uniacid,
                'title' =>  $list['title'],
                'template_id' => $template_date['template_id'],
                'keyword_id'=>implode(",", $list['key_val']),
                'title_id'=>$list['id'],
                'offset'=>$list['offset']
            ]);
           return $ret;
        }
   }

    public function edit()
    {
        if (request()->id) {
            $min_small = new MinAppTemplateMessage;
            $temp_date = $min_small::getTemp(request()->id);//获取数据表中的数据
            $small = new SmallProgramNotice();
            $key_list = $small->getTemplateKey($temp_date->title_id);
        }
        if (request()->key_val) {
            foreach (request()->key_val as $value){
                $keyWord_list[] = explode(":",$value)[0];
            }
            $template_date = $small->getAddTemplate($temp_date->title_id, $keyWord_list);
            if ($template_date['errcode'] != 0 || !isset($template_date['errcode'])){
                return $this->message('修改模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
            }
            $del_temp = $small->deleteTemplate($temp_date->template_id);//删除原来的模板
            $temp_date->keyword_id = implode(",", request()->key_val);
            $temp_date->template_id =$template_date['template_id'];
            $ret = $temp_date->save();
            if (!$ret) {
                return $this->message('修改模板失败', Url::absoluteWeb('setting.small-program.index'), 'error');
            }
            return $this->message('修改模板成功', Url::absoluteWeb('setting.small-program.index'));
        }

        if ($key_list['errcode']==0){
            $keyWord = $key_list['keyword_list'];
        }
        return view('setting.small-program.detail', [
            'keyWord'=>$keyWord,
            'is_edit'=>0,
            'title'=>$temp_date->title,
            'id'=>$temp_date->title_id,
            'list'=>$small->getAllTemplateList($temp_date->offset)['list'],
            'page'=>$temp_date->offset,
            'url'=>'setting.small-program.save'
        ])->render();
        }

    public function notice()
    {
        $notice = \Setting::get('min_app.notice');
        $requestModel = \YunShop::request()->yz_notice;
        $temp_list = MinAppTemplateMessage::getList();
        if (!empty($requestModel)) {
            if (\Setting::set('min_app.notice', $requestModel)) {
                return $this->message(' 消息提醒设置成功', Url::absoluteWeb('setting.small-program.notice'));
            } else {
                $this->error('消息提醒设置失败');
            }
        }
        return view('setting.small-program.notice', [
            'set' => $notice,
            'temp_list' => $temp_list
        ])->render();
    }

        function del()
        {
            if (request()->id) {
                $min_small = new MinAppTemplateMessage;
                $small = new SmallProgramNotice();
                $temp = $min_small::getTemp(request()->id);
                if (empty($temp)) {
                    return $this->message('找不到该模板', Url::absoluteWeb('setting.small-program.index'), 'error');
                }
//                dd($temp->template_id);
                $ret = $small->deleteTemplate($temp->template_id);
                if ($ret['errcode'] == 0) {
                    $min_small->delTempDataByTempId(request()->id);
                    $kwd = request()->keyword;
                    $list = MinAppTemplateMessage::getList();
                    return view('setting.small-program.list', [
                        'list' => $list->toArray(),
                        'kwd' => $kwd,
                        'url' => 'setting.small-program.save'
                    ])->render();
                }
            }
        }

    }