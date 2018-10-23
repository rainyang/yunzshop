<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/23
 * Time: 下午2:34
 */

namespace app\common\modules\express;

use Ixudra\Curl\Facades\Curl;

class KDN
{
    private $eBusinessID;
    private $appKey;
    private $reqURL;

    public function __construct($eBusinessID, $appKey, $reqURL)
    {
        $this->eBusinessID = $eBusinessID;
        $this->appKey = $appKey;
        $this->reqURL = $reqURL;
    }

    public function getTraces($comCode, $expressSn, $orderSn = '')
    {
        $requestData = json_encode(
            [
                'OrderCode' => $orderSn,
                'ShipperCode' => $this->mappingCom($comCode),
                'LogisticCode' => $expressSn,
            ]
        );

        $datas = array(
            'EBusinessID' => $this->eBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );

        $datas['DataSign'] = $this->encrypt($requestData);
        $response = Curl::to($this->reqURL)->withData($datas)
            ->asJsonResponse(true)->get();

        return $this->format($response);
    }

    private function format($response)
    {
        $result = [];
        foreach ($response['Traces'] as $trace) {
            $result['data'][] = [
                'time' => $trace['AcceptTime'],
                'ftime' => $trace['AcceptTime'],
                'context' => $trace['AcceptStation'],
                'location' => null,
            ];
        }
        $result['state'] = $response['State'];
        return $result;
    }

    private function mappingCom($comCode)
    {
        $companies = [
            'shunfeng' => 'SF',
            'huitongkuaidi' => 'HTKY',
            'zhongtong' => 'ZTO',
            'shentong' => 'STO',
            'yuantong' => 'YTO',
            'yunda' => 'YD',
            'youzhengguonei' => 'YZPY',
            'ems' => 'EMS',
            'tiantian' => 'HHTT',
            'youshuwuliu' => 'UC',
            'debangwuliu' => 'DBL',
            'zhaijisong' => 'ZJS',
            'tnt' => 'TNT',
            'ups' => 'UPS',
            'fedex' => 'FEDEX',
        ];
        return array_get($companies, $comCode, strtoupper($comCode));
    }

    private function encrypt($data)
    {
        return urlencode(base64_encode(md5($data . $this->appKey)));
    }
}