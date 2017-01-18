<?php
/*=============================================================================
#     FileName: excel.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:32:10
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Excel
{
    protected function column_str($key)
    {
        $array = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ'
        );
        return $array[$key];
    }
    protected function column($key, $columnnum = 1)
    {
        return $this->column_str($key) . $columnnum;
    }
    function export($list, $params = array())
    {
        if (PHP_SAPI == 'cli') {
            die('This example should only be run from a Web Browser');
        }
        ob_end_clean();
        require_once IA_ROOT . '/addons/sz_yi/core/inc/phpexcel/PHPExcel.php';
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator("芸众商城")->setLastModifiedBy("芸众商城")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("report file");
        $sheet  = $excel->setActiveSheetIndex(0);
        $rownum = 1;
        foreach ($params['columns'] as $key => $column) {
            $sheet->setCellValue($this->column($key, $rownum), $column['title']);
            if (!empty($column['width'])) {
                $sheet->getColumnDimension($this->column_str($key))->setWidth($column['width']);
            }
        }
        $rownum++;

        foreach ($list as $row) {
            $len = count($params['columns']);
            for ($i = 0; $i < $len; $i++) {
                $value = $row[$params['columns'][$i]['field']];
                $value = @iconv("utf-8", "gbk", $value);
                $value = @iconv("gbk", "utf-8", $value);
               /* if ($params['columns'][$i]['field'] == 'nickname') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }

                if ($params['columns'][$i]['field'] == 'realname') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }

                if ($params['columns'][$i]['field'] == 'mrealname') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }

                if ($params['columns'][$i]['field'] == 'expresssn') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }

                if ($params['columns'][$i]['field'] == 'remark') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }

                if ($params['columns'][$i]['field'] == 'salerinfo') {
                    $value = @iconv("utf-8", "gbk", $value);
                    $value = @iconv("gbk", "utf-8", $value);
                }*/


                $sheet->setCellValueExplicit($this->column($i, $rownum), $value, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $rownum++;
        }
        $excel->getActiveSheet()->setTitle($params['title']);
        $filename = $params['title'] . '-' . date('Y-m-d H:i', time());
        header('Content-Type: application/vnd.ms-excel');
        //header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $writer->save('php://output');
        exit;
    }

    function exportOrder($list, $params = array(), $page = 1, $page_total)
    {
        if (PHP_SAPI == 'cli') {
            die('This example should only be run from a Web Browser');
        }
        static $excel;
        if (!isset($excel)) {
            require_once IA_ROOT . '/addons/sz_yi/core/inc/phpexcel/PHPExcel.php';
            $excel = new PHPExcel();
        }
        $excel->getProperties()->setCreator("芸众商城")->setLastModifiedBy("芸众商城")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("report file");
        $sheet  = $excel->setActiveSheetIndex(0);
        $rownum = 1;
        foreach ($params['columns'] as $key => $column) {
            $sheet->setCellValue($this->column($key, $rownum), $column['title']);
            if (!empty($column['width'])) {
                $sheet->getColumnDimension($this->column_str($key))->setWidth($column['width']);
            }
        }
        $rownum++;
        foreach ($list as $row) {
            $len = count($params['columns']);
            for ($i = 0; $i < $len; $i++) {
                $value = $row[$params['columns'][$i]['field']];
                $value = @iconv("utf-8", "gbk", $value);
                $value = @iconv("gbk", "utf-8", $value);
                $sheet->setCellValueExplicit($this->column($i, $rownum), $value, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $rownum++;
        }
        $excel->getActiveSheet()->setTitle($params['title']);
        $filename = $params['title']."-". $page;
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        if (!is_dir('IA_ROOT . "/addons/sz_yi/data/excel')) {
            mkdir (IA_ROOT . "/addons/sz_yi/data/excel/");
        }
        $writer->save(IA_ROOT . "/addons/sz_yi/data/excel/" . $filename . ".xls");
        if ($page == $page_total) {
            load()->func('file');
            $filename = IA_ROOT . "/addons/sz_yi/data/". time() . "down.zip";
            $time = time();
            $zip = new ZipArchive (); // 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
            if ($zip->open ( $filename, ZIPARCHIVE::CREATE ) !== TRUE) {
                exit ( '无法打开文件，或者文件创建失败' );
            }
            //$fileNameArr 就是一个存储文件路径的数组 比如 array('/a/1.jpg,/a/2.jpg....');
            $fileNameArr = file_tree(IA_ROOT . "/addons/sz_yi/data/excel");
            foreach ($fileNameArr as $val ) {
                // 当你使用addFile添加到zip包时，必须确保你添加的文件是存在的，否则close时会返回FALSE，而且使用addFile时，即使文件不存在也会返回TRUE
                if(file_exists(IA_ROOT . "/addons/sz_yi/data/excel" . $val)){
                    $zip->addFile (IA_ROOT . "/addons/sz_yi/data/excel" . $val,basename($val) ); // 第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                }else{
                    exit ( '向zip中添加的文件不存在' );
                }
            }
            $zip->close (); // 关闭
            foreach ($fileNameArr as $val ) {
                if(file_exists(IA_ROOT . "/addons/sz_yi/data/excel" . $val)){
                    file_delete(IA_ROOT . "/addons/sz_yi/data/excel" . $val);
                }
            }
            //下面是输出下载;

            $url = "http://". $_SERVER['SERVER_NAME']."/addons/sz_yi/data/".$time ."down.zip";
            $backurl = "http://". $_SERVER['SERVER_NAME']."/web/index.php?c=site&a=entry&op=display&do=order&m=sz_yi"; 
            echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;"><a style="color:red;text-decorationnone;"  href="'.$url.'">点击获取下载文件</a><a style="color:#616161"  href="'.$backurl.'">返回</a><div>';

            // header ( "Cache-Control: max-age=0" );
            // header ( "Content-Description: File Transfer" );
            // header ( 'Content-disposition: attachment; filename=' . basename ($filename)); // 文件名
            // header ( "Content-Type: application/zip" ); // zip格式的
            // header ( "Content-Transfer-Encoding: binary" ); // 告诉浏览器，这是二进制文件
            // header ( 'Content-Length: ' . filesize ( $filename ) ); // 告诉浏览器，文件大小
            // @readfile ( $filename );//输出文件;
        } 
    }
    public function import($excefile)
    {
        global $_W;
        require_once IA_ROOT . '/addons/sz_yi/core/inc/phpexcel/PHPExcel.php';
        require_once IA_ROOT . '/addons/sz_yi/core/inc/phpexcel/PHPExcel/IOFactory.php';
        require_once IA_ROOT . '/addons/sz_yi/core/inc/phpexcel/PHPExcel/Reader/Excel5.php';
        $path = IA_ROOT . "/addons/sz_yi/data/tmp/";
        if (!is_dir($path)) {
            load()->func('file');
            mkdirs($path, '0777');
        }
        $file     = time() . $_W['uniacid'] . ".xls";
        $filename = $_FILES[$excefile]['name'];
        $tmpname  = $_FILES[$excefile]['tmp_name'];
        if (empty($tmpname)) {
            message('请选择要上传的Excel文件!', '', 'error');
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext != 'xls') {
            message('请上传 xls 格式的Excel文件!', '', 'error');
        }
        $uploadfile = $path . $file;
        $result     = move_uploaded_file($tmpname, $uploadfile);
        if (!$result) {
            message('上传Excel 文件失败, 请重新上传!', '', 'error');
        }
        $reader             = PHPExcel_IOFactory::createReader('Excel5');
        $excel              = $reader->load($uploadfile);
        $sheet              = $excel->getActiveSheet();
        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn();
        $highestColumnCount = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $values             = array();
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowValue = array();
            for ($col = 0; $col < $highestColumnCount; $col++) {
                $rowValue[] = $sheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            $values[] = $rowValue;
        }
        return $values;
    }
}
