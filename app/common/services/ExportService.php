<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/25
 * Time: 上午11:31
 */

namespace app\common\services;


class ExportService
{
    public function export($file_name, $export_data)
    {
        \Excel::create($file_name, function ($excel) use ($export_data) {
            $excel->setTitle('Office 2005 XLSX Document');
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");
            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });
        })->export('xls');
    }
}