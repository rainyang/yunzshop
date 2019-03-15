<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/3/14
 * Time: 11:15
 */

namespace app\platform\modules\application\controllers;

use app\platform\controllers\BaseController;
use app\platform\modules\system\models\SystemSetting;
use vendor\aliyuncs\ossSdkPhp\src\OSS\OssClient;
use OSS\Core\OssException;


class AliOssController extends BaseController
{
	protected $accessKeyId;
	protected $accessKeySecret;
	protected $endpoint;
	protected $bucket;
    protected $ossClient;
	
	public function __construct($accessKeyId, $accessKeySecret, $endpoint, $bucket)
	{
		$this->accessKeyId = $accessKeyId;
		$this->accessKeySecret = $accessKeySecret;
		$this->endpoint = $endpoint;
		$this->bucket = $bucket;

		$setting = SystemSetting::settingLoad();

		$this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecr, $this->endpoint);
	}

	public function getSetting()
	{

		try {
		    $this->ossClient->createBucket($this->bucket);

		} catch (OssException $e) {
		    print $e->getMessage();
		}
	}
}