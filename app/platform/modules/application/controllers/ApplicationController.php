<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\application\models\UniacidApp;

class ApplicationController extends BaseController
{
	public function index()
	{
		return View('admin.application.index');
	}

	public function add()
	{
		dd(request()->input, request());
		if (request()->input) {
			UniacidApp::fill();
			if (UniacidApp::save()) {
				return $this->commonRedirect('route', '添加成功', 'success');
			} else {
				return $this->commonRedirect('', '添加失败', 'failed');
			}
		}
		return View('admin.application.form');
	}

	public function update()
	{
		return View('admin.application.form');
	}

	//加入回收站 删除
	public function delete()
	{

	}

	//启用禁用
	public function switchStatus()
	{
	}
}