<?php

namespace app\platform\controllers;

class ClearController extends BaseController
{
    public function index()
	{
        \Cache::flush();
		return $this->successJson('操作成功');
	}
}
