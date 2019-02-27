<?php

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;

class AppuserController extends BaseController
{

	public function index()
	{
		return View('admin.appuser.index');
	}

	public function add()
	{
		return View('admin.appuser.form');
	}

	public function update()
	{
		return View('admin.appuser.form');
	}

	public function delete()
	{

	}
}