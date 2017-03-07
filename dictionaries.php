<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/7
 * Time: 下午1:33
 */

error_reporting(0);
require '../../framework/bootstrap.inc.php';
require '../../addons/sz_yi/defines.php';
require '../../addons/sz_yi/core/inc/functions.php';
require '../../addons/sz_yi/core/inc/plugin/plugin_model.php';
global $_W, $_GPC;
set_time_limit(0);

//$sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid WHERE a.default_acid <> 0";
//$sets = pdo_fetchall($sql);
if($_GPC['table']){

    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$_GPC['table']}' ";
    $sql .= "AND TABLE_SCHEMA = 'yunzv2' ";
    //$sql .= " group BY COLUMN_NAME";
    $data = pdo_fetchall($sql);
    $html = "|字段|类型|空|默认|注释|\r\n";
    $html .= "|:---- |:------- |:--- |-- -|------ |\r\n";
    foreach ($data as $value) {
        $html .= "|";
        $html .= $value['COLUMN_NAME'];
        $html .= "|";
        $html .= $value['DATA_TYPE'];
        $html .= "(";
        $html .= $value['CHARACTER_MAXIMUM_LENGTH'] ? $value['CHARACTER_MAXIMUM_LENGTH'] : '11';
        $html .= ")";
        $html .= "|";
        $html .= $value['IS_NULLABLE'];
        $html .= "|";
        $html .= $value['COLUMN DEFAULT'];
        $html .= "|";
        $comment = '';
        if($value['COLUMN_NAME'] == 'created_at'){
            $comment = '创建时间';
        }elseif($value['COLUMN_NAME'] == 'updated_at'){
            $comment = '更新时间';
        }elseif($value['COLUMN_NAME'] == 'deleted_at'){
            $comment = '删除时间';
        }
        $html .= $value['COLUMN_COMMENT'] ? $value['COLUMN_COMMENT'] : $comment;
        $html .= "|";
        $html .= "\r\n";



    }
     //id	  |int(10)     |否	|	 |	           |
    //echo "<pre>"; print_r($data);

    echo "<pre>"; print_r($html);
}

?>
<form action="" method="post" name="myform">
    表明:<input type="text" name="table" value="">
    <input type="submit" value="提交">
</form>





