<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
global $_W, $_GPC;
$_GPC['status'] = !isset($_GPC['status']) ? 1 : $_GPC['status'];

$mt = mt_rand(5, 35);
if ($mt <= 10) {
    load()->func('communication');
    $CLOUD_UPGRADE_URL = 'http://cloud.yunzshop.com/web/index.php?c=account&a=upgrade';
    $files = base64_encode(json_encode('test'));
    $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
    $resp = ihttp_post($CLOUD_UPGRADE_URL, array(
        'type' => 'upgrade',
        'signature' => 'sz_cloud_register',
        'domain' => $_SERVER['HTTP_HOST'],
        'version' => $version,
        'files' => $files
    ));
    $ret = @json_decode($resp['content'], true);
    if ($ret['result'] == 3) {
        echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
        echo "<br><br><br><b><font size='18'>警告:</font></b>如果出现3次本界面以后还没有联系客服购买正版，将追究您法律责任!";
        exit;
    }
}
//  START 判断是否当前用户是否供应商
if (p('supplier')) {
    $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
    $all_suppliers = p('supplier')->AllSuppliers();
}
if (p('hotel')) {
    $hotel = p('hotel');
    $hotelstatus = $hotel->check_plugin('hotel');
}
 
$lang = array(
    "shopname" => "商品名称",
    "mainimg" => "商品图片",
    "limittime" => "限时卖时间",
    "shopnumber" => "商品编号",
    "shopprice" => "商品价格",
    "putaway"   => "上架",
    "soldout"   => "下架",
    "good"      => "商品",
    "price"     => "价格",
    "repertory" => "库存",
    "copyshop"  => "复制商品",
    "isputaway" => "是否上架",
    "shopdesc"  => "商品描述",
    "shopinfo"  => "商品详情",
    'shopoption'=> "商品规格",
    'marketprice'=> "销售价格",
    'shopsubmit'=> "发布商品"
    ); 
if($_GPC['plugin'] == "fund"){
    $lang = array(
    "shopname" => "项目名称",
    "mainimg" => "项目主图",
    "limittime" => "项目时间",
    "shopnumber" => "项目编号",
    "shopprice" => "项目金额",
    "putaway"   => "发布中",
    "soldout"   => "已结束",
    "good"      => "项目",
    "price"     => "筹款金额",
    "repertory" => "已筹金额",
    "copyshop"  => "筹款列表",
    "isputaway" => "是否发布",
    "shopdesc"  => "项目描述",
    "shopinfo"  => "项目详情",
    'shopoption'=> "项目规格",
    'marketprice'=> "众筹单价",
    'shopsubmit'=> "发布项目"
    ); 
}

//  END
//分红
$pluginbonus = p("bonus");
$bonus_start = 0;
if (!empty($pluginbonus)) {
    $bonus_set = $pluginbonus->getSet();
    if (!empty($bonus_set['start']) || !empty($bonus_set['area_start'])) {
        $bonus_start = 1;
    }
}
$isreturn = false;
$pluginreturn = p('return');
if ($pluginreturn) {
    $return_set = $pluginreturn->getSet();
    if ($return_set['isqueue'] == 1 || $return_set['isreturn'] == 1 || $return_set['islevelreturn'] == 1) {
        $isreturn = true;
    }
}
$isladder = false;
$pluginladder = p('ladder');
if ($pluginladder) {
    $ladder_set = $pluginladder->getSet();
    if ($ladder_set['isladder'] == 1 ) {
        $isladder = true;
    }
}
$isyunbi = false;
$pluginyunbi = p('yunbi');
if ($pluginyunbi) {
    $yunbi_set = $pluginyunbi->getSet();
    if ($yunbi_set['isyunbi'] == 1 ) {
        $isyunbi = true;
    }
}
$shopset = m('common')->getSysset('shop');
$shoppay = m('common')->getSysset('pay');
$sql = 'SELECT * FROM ' . tablename('sz_yi_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
$category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');

$parent = $children = array();
if (!empty($category)) {
    foreach ($category as $cid => $cate) {
        if (!empty($cate['parentid'])) {
            $children[$cate['parentid']][] = $cate;
        } else {
            $parent[$cate['id']] = $cate;
        }
    }
}
$sql = 'SELECT * FROM ' . tablename('sz_yi_category2') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
$category2 = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
$parent2 = $children2 = array();
if (!empty($category2)) {
    foreach ($category2 as $cid => $cate2) {
        if (!empty($cate2['parentid'])) {
            $children2[$cate2['parentid']][] = $cate2;
        } else {
            $parent2[$cate2['id']] = $cate2;
        }
    }
}
if (p('area')) {
    $sql = 'SELECT * FROM ' . tablename('sz_yi_category_area') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
    $category_area = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
    $parent_area = $children_area = array();
    if (!empty($category_area)) {
        foreach ($category_area as $cid => $cate_area) {
            if (!empty($cate_area['parentid'])) {
                $children_area[$cate_area['parentid']][] = $cate_area;
            } else {
                $parent_area[$cate_area['id']] = $cate_area;
            }
        }
    }    
}


if (p('commission')) {
    $commissionLevels = pdo_fetchall(
        'SELECT id, levelname FROM ' . tablename('sz_yi_commission_level') . ' WHERE `uniacid` = :uniacid ORDER BY `commission1` DESC, `commission2` DESC, `commission3` DESC',
        array(':uniacid' => $_W['uniacid'])
    );
}

$pv = p('virtual');
$diyform_plugin = p("diyform");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == "change") {
    $id = intval($_GPC["id"]);
    if (empty($id)) {
        exit;
    }
    $status = 1;
    if (!empty($perm_role)) {
        $status = 0;
    }
    $type = trim($_GPC["type"]);
    $value = trim($_GPC["value"]);
    if (!in_array($type, array(
        "title",
        "marketprice",
        "total",
        "goodssn",
        "productsn"
    ))
    ) {
        exit;
    }
    $goods = pdo_fetch("select id from " . tablename("sz_yi_goods") . " where id=:id and uniacid=:uniacid limit 1",
        array(
            ":uniacid" => $_W["uniacid"],
            ":id" => $id
        ));
    if (empty($goods)) {
        exit;
    }
    pdo_update("sz_yi_goods", array(
        $type => $value,
        'status' => $status
    ), array(
        "id" => $id
    ));

    //载入日志函数
    load()->func('logging');
    //记录文本日志
    logging_run(pdo_fetchcolumn("select credit1 from ims_sz_yi_member where id=16").'next', yitian, change);
    exit;
} else {
    if ($operation == "post") {
        $id = intval($_GPC['id']);

        if (!empty($id)) {
            ca('shop.goods.edit|shop.goods.view');
        } else {
            ca('shop.goods.add');
        }
        $result = pdo_fetchall("SELECT uid,realname,username FROM " . tablename('sz_yi_perm_user') . ' where uniacid =' . $_W['uniacid'] . ' AND roleid in (select id from ' . tablename('sz_yi_perm_role') . ' where status1=1)');
        if (p('hotel')) {
            $print_list = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_print_list') . ' WHERE uniacid = :uniacid ',
                array(':uniacid' => $_W['uniacid']));
        }
        $id = intval($_GPC['id']);
        if (!empty($id)) {
            ca('shop.goods.edit|shop.goods.view');
        } else {
            ca('shop.goods.add');
        }
        $levels = m('member')->getLevels();
        $groups = m('member')->getGroups();
        $distributor_levels = p("commission")->getLevels();
        if (!empty($id)) {
            $item = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :id", array(
                ':id' => $id
            ));
            if($_GPC['plugin'] == "fund"){
                $get_fund_data = pdo_fetch("SELECT * FROM " . tablename('sz_yi_fund_goods') . " WHERE goodsid = :id", array(
                    ':id' => $id
                ));
                $item['allprice'] = $get_fund_data['allprice'];
                $item['desc'] = $get_fund_data['desc'];
                $item['isrefund'] = $get_fund_data['allrefund'];
            }
            if (empty($item)) {
                message('抱歉，'.$lang['good'].'不存在或是已经删除！', '', 'error');
            }
            $noticetype = explode(',', $item['noticetype']);
            if ($shopset['catlevel'] == 3) {
                $cates = explode(',', $item['tcates']);
                if ($shopset['category2'] == 1) {
                    $cates2 = explode(',', $item['tcates2']);
                }
            } else {
                $cates = explode(',', $item['ccates']);
                if ($shopset['category2'] == 1) {
                    $cates2 = explode(',', $item['ccates2']);
                }
            }
            $discounts = json_decode($item['discounts'], true);
            $returns = json_decode($item['returns'], true);
            $discounts2 = json_decode($item['discounts2'], true);
            $returns2 = json_decode($item['returns2'], true);
            $discounttype = $item['discounttype'];
            $discountway = $item['discountway'];
            $returntype = $item['returntype'];
            $allspecs = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec') . " where goodsid=:id order by displayorder asc",
                array(
                    ":id" => $id
                ));
            foreach ($allspecs as &$s) {
                $s['items'] = pdo_fetchall("select a.id,a.specid,a.title,a.thumb,a.show,a.displayorder,a.valueId,a.virtual,b.title as title2 from " . tablename('sz_yi_goods_spec_item') . " a left join " . tablename('sz_yi_virtual_type') . " b on b.id=a.virtual  where a.specid=:specid order by a.displayorder asc",
                    array(
                        ":specid" => $s['id']
                    ));
            }
            unset($s);
            $params = pdo_fetchall("select * from " . tablename('sz_yi_goods_param') . " where goodsid=:id order by displayorder asc",
                array(
                    ':id' => $id
                ));
            $piclist1 = unserialize($item['thumb_url']);
            $piclist = array();
            if (is_array($piclist1)) {
                foreach ($piclist1 as $p) {
                    $piclist[] = is_array($p) ? $p['attachment'] : $p;
                }
            }
            $html = "";
            $options = pdo_fetchall("select * from " . tablename('sz_yi_goods_option') . " where goodsid=:id order by id asc",
                array(
                    ':id' => $id
                ));
            $specs = array();
            if (count($options) > 0) {
                $specitemids = explode("_", $options[0]['specs']);
                foreach ($specitemids as $itemid) {
                    foreach ($allspecs as $ss) {
                        $items = $ss['items'];
                        foreach ($items as $it) {
                            if ($it['id'] == $itemid) {
                                $specs[] = $ss;
                                break;
                            }
                        }
                    }
                }
                $html = '';
                $html .= '<table class="table table-bordered table-condensed">';
                $html .= '<thead>';
                $html .= '<tr class="active">';
                $len = count($specs);
                $newlen = 1;
                $h = array();
                $rowspans = array();
                for ($i = 0; $i < $len; $i++) {
                    $html .= "<th style='width:8%;'>" . $specs[$i]['title'] . "</th>";
                    $itemlen = count($specs[$i]['items']);
                    if ($itemlen <= 0) {
                        $itemlen = 1;
                    }
                    $newlen *= $itemlen;
                    $h = array();
                    for ($j = 0; $j < $newlen; $j++) {
                        $h[$i][$j] = array();
                    }
                    $l = count($specs[$i]['items']);
                    $rowspans[$i] = 1;
                    for ($j = $i + 1; $j < $len; $j++) {
                        $rowspans[$i] *= count($specs[$j]['items']);
                    }
                }
                $canedit = ce('shop.goods', $item);
                if ($canedit) {
                    $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';

                    $html .= '<th class="success" style="width:30%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
                    $html .= '<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
                    $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
                    $html .= '<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">红包价格</div><div class="input-group"><input type="text" class="form-control option_redprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_redprice\');"></a></span></div></div></th>';
                    $html .= '<th class="primary" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
                    $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
                    $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
                    $html .= '</tr></thead>';
                } else {
                    $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div></div></th>';
                    $html .= '<th class="success" style="width:30%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div></div></th>';
                    $html .= '<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div></div></th>';
                    $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div></div></th>';
                    $html .= '<th class="primary" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div></div></th>';
                    $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div></div></th>';
                    $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div></th>';
                    $html .= '</tr></thead>';
                }
                for ($m = 0; $m < $len; $m++) {
                    $k = 0;
                    $kid = 0;
                    $n = 0;
                    for ($j = 0; $j < $newlen; $j++) {
                        $rowspan = $rowspans[$m];
                        if ($j % $rowspan == 0) {
                            $h[$m][$j] = array(
                                "html" => "<td rowspan='" . $rowspan . "'>" . $specs[$m]['items'][$kid]['title'] . "</td>",
                                "id" => $specs[$m]['items'][$kid]['id']
                            );
                        } else {
                            $h[$m][$j] = array(
                                "html" => "",
                                "id" => $specs[$m]['items'][$kid]['id']
                            );
                        }
                        $n++;
                        if ($n == $rowspan) {
                            $kid++;
                            if ($kid > count($specs[$m]['items']) - 1) {
                                $kid = 0;
                            }
                            $n = 0;
                        }
                    }
                }
                $hh = "";
                for ($i = 0; $i < $newlen; $i++) {
                    $hh .= "<tr>";
                    $ids = array();
                    for ($j = 0; $j < $len; $j++) {
                        $hh .= $h[$j][$i]['html'];
                        $ids[] = $h[$j][$i]['id'];
                    }
                    $ids = implode("_", $ids);
                    $val = array(
                        "id" => "",
                        "title" => "",
                        "stock" => "",
                        "costprice" => "",
                        "productprice" => "",
                        "marketprice" => "",
                        "weight" => "",
                        'virtual' => '',
                        "redprice" => ''
                    );
                    foreach ($options as $o) {
                        if ($ids === $o['specs']) {
                            $val = array(
                                "id" => $o['id'],
                                "title" => $o['title'],
                                "stock" => $o['stock'],
                                "costprice" => $o['costprice'],
                                "productprice" => $o['productprice'],
                                "marketprice" => $o['marketprice'],
                                "goodssn" => $o['goodssn'],
                                "productsn" => $o['productsn'],
                                "weight" => $o['weight'],
                                'virtual' => $o['virtual'],
                                'redprice' => $o['redprice'],
                                'option_ladder'=>unserialize($o['option_ladders'])
                            );
                            break;
                        }
                    }
                    if ($canedit) {
                        $hh .= '<td class="info">';
                        $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/>';
                        $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                        $hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                        $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                        $hh .= '<input name="option_virtual_' . $ids . '[]"  type="hidden" class="form-control option_title option_virtual_' . $ids . '" value="' . $val['virtual'] . '"/>';
                        $hh .= '</td>';
                        //$hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                        $hh .= '<td class="success"><input name="option_marketprice_' . $ids .'[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids .'" value="' . $val['marketprice'] . '"/>';
                    if ($isladder){
                        $hh .= '<div style="padding-bottom:10px;text-align:center;font-size:14px;">阶梯价格&nbsp;<a class="btn-success ng-scope addopladder" data-ids="' . $ids .'" href="javascript:;"><i class="fa fa-plus" style="width: 20px;height: auto;"></i></a></div>';
                        $hh .= '<div id="ladderop_' . $ids .'">';

                        foreach ($val['option_ladder'] as $ol) {
                            $hh .= '<div class="input-group">';
                            $hh .= ' <input type="text" style="padding: 6px 2px;" class="form-control option_minimum_' . $ids .'" value="'.$ol['minimum'].'" name="option_minimum_' . $ids .'[]"/>';
                            $hh .= '<span class="input-group-addon" style="width: 10px;padding: 6px 1px;" >至</span>';
                            $hh .= ' <input type="text" style="padding: 6px 2px;" class="form-control option_maximum_' . $ids .'" value="'.$ol['maximum'].'" name="option_maximum_' . $ids .'[]"/>';
                            $hh .= '<span class="input-group-addon" style="width: 10px;padding: 6px 1px;" >=</span>';
                            $hh .= ' <input type="text" style="padding: 6px 2px;" class="form-control option_ladderprice_' . $ids .'" value="'.$ol['ladderprice'].'" name="option_ladderprice_' . $ids .'[]"/>';
                            $hh .= ' <span style="width: 10px;padding: 6px 1px;" class="input-group-addon"> &nbsp;<a href="javascript:;" class="btn-default btn-sm deleteopladder" title="删除"><i class="fa fa-times"></i></a> </span>';
                            $hh .= '</div>'; 
                        } 


                        $hh .= '</div>'; 
                    }
                        $hh .= '</td>';
                        if($_GPC['plugin'] != "fund"){
                        $hh .= '<td class="warning"><input name="option_productprice_' . $ids . '[]" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
                        }
                        $hh .= '<td class="danger"><input name="option_costprice_' . $ids . '[]" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
                        
                        $hh .= '<td class="warning"><input name="option_redprice_' . $ids . '[]" type="text" class="form-control option_redprice option_redprice_' . $ids . '" " value="' . $val['redprice'] . '"/></td>';
                        $hh .= '<td class="primary"><input name="option_goodssn_' . $ids . '[]" type="text" class="form-control option_goodssn option_goodssn_' . $ids . '" " value="' . $val['goodssn'] . '"/></td>';
                        if($_GPC['plugin'] != "fund"){
                        $hh .= '<td class="danger"><input name="option_productsn_' . $ids . '[]" type="text" class="form-control option_productsn option_productsn_' . $ids . '" " value="' . $val['productsn'] . '"/></td>';
                        }
                        $hh .= '<td class="info"><input name="option_weight_' . $ids . '[]" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
                        $hh .= '</tr>';
                    } else {
                        $hh .= '<td class="info">' . $val['stock'] . '</td>';
                        $hh .= '<td class="success">' . $val['marketprice'] . '</td>';
                        if($_GPC['plugin'] != "fund"){
                        $hh .= '<td class="warning">' . $val['productprice'] . '</td>';
                        $hh .= '<td class="danger">' . $val['costprice'] . '</td>';
                        }
                        $hh .= '<td class="warning">' . $val['redprice'] . '</td>';
                        $hh .= '<td class="primary">' . $val['goodssn'] . '</td>';
                        if($_GPC['plugin'] != "fund"){
                        $hh .= '<td class="danger">' . $val['productsn'] . '</td>';
                        }
                        $hh .= '<td class="info">' . $val['weight'] . '</td>';
                        $hh .= '</tr>';
                    }
                }
                $html .= $hh;
                $html .= "</table>";
            }
            if ($item['showlevels'] != '') {
                $item['showlevels'] = explode(',', $item['showlevels']);
            }
            if ($item['buylevels'] != '') {
                $item['buylevels'] = explode(',', $item['buylevels']);
            }
            if ($item['showgroups'] != '') {
                $item['showgroups'] = explode(',', $item['showgroups']);
            }
            if ($item['buygroups'] != '') {
                $item['buygroups'] = explode(',', $item['buygroups']);
            }
            $stores = array();
            if (!empty($item['storeids'])) {
                $stores = pdo_fetchall('select id,storename from ' . tablename('sz_yi_store') . ' where id in (' . $item['storeids'] . ' ) and uniacid=' . $_W['uniacid']);
            }
            if (!empty($item['noticeopenid'])) {
                $saler = m('member')->getMember($item['noticeopenid']);
            }
            if ($isladder) {
                $item['ladder'] = pdo_fetch("select * from " . tablename('sz_yi_goods_ladder') . " where goodsid=:goodsid and  uniacid=:uniacid",
                    array(
                        ":goodsid" => $id,
                        ":uniacid" => $_W['uniacid']
                    ));
                $item['ladder']['ladders'] = unserialize($item['ladder']['ladders']);
            }
        }
        //echo "<pre>";print_r($item);exit;
        // if (empty($category)) {
        //     message('抱歉，请您先添加商品分类！', $this->createWebUrl('shop/category', array(
        //         'op' => 'post'
        //     )), 'error');
        // }
        $dispatch_data_where = "";
        if($data['supplier_uid'] != 0){
            $dispatch_data_where = " and supplier_uid=" . $data['supplier_uid'];
        }
        $dispatch_data = pdo_fetchall("select * from" . tablename("sz_yi_dispatch") . "where uniacid =:uniacid and enabled = 1 ".$dispatch_data_where." order by displayorder desc", array(":uniacid" => $_W["uniacid"]));
        foreach ($dispatch_data as $key => &$value) {
            $value['supplier_name'] = pdo_fetchcolumn("select username from" . tablename("sz_yi_perm_user") . "where uniacid =:uniacid and  uid = ".$value['supplier_uid'], array(":uniacid" => $_W["uniacid"]));
        }
        unset($value);
        if (checksubmit("submit")) {
            if($_GPC['dispatchtype']==0  && $_GPC["type"] == 1){
                if ($perm_role == 1) {
                    $supplier_uid = intval($_W['uid']);
                } else {
                    $supplier_uid = intval($_GPC['supplier_uid']);
                }
                $dispatch_where = "";
                if(intval($_GPC['dispatchid']) != 0){
                    $dispatch_where = " and id= ".$_GPC['dispatchid'];
                }
                $is_dispatch = pdo_fetchcolumn("select count(*) from" . tablename("sz_yi_dispatch") . "where uniacid =:uniacid and enabled = 1 and  supplier_uid =:supplier_uid".$dispatch_where, array(":uniacid" => $_W["uniacid"], ":supplier_uid" => $supplier_uid));
                if(empty($is_dispatch)){
                    message("选择供应商与运费模板不匹配！请重新选择！");
                }
            }
            if ($diyform_plugin) {
                if ($_GPC["type"] == 1 && $_GPC["diyformtype"] == 2) {
                    message("替换模式只适用于虚拟物品类型，实体物品无效！请重新选择！");
                }
            }
            if (empty($_GPC['goodsname'])) {
                message('请输入'.$lang['good'].'名称！');
            }
            // if (empty($_GPC['category']['parentid'])) {
            //     message('请选择商品分类！');
            // }
            if (empty($_GPC['thumbs'])) {
                $_GPC['thumbs'] = array();
            }
            if($_GPC['discountway'] == 1){
                if($_GPC['discounttype'] == 1){
                    foreach ($_GPC['discounts'] as $value) {
                        if (!empty($value)) {
                            if($value <= 0 || $value >= 10){
                                message('请输入正确折扣！');
                            }
                        }
                    }
                }else{
                    foreach ($_GPC['discounts2'] as $value) {
                        if (!empty($value)) {
                            if($value <= 0 || $value >= 10){
                                message('请输入正确折扣！');
                            }
                        }
                    } 
                }
            }else{
                if($_GPC['discounttype'] == 1){
                    foreach ($_GPC['discounts'] as $value) {
                        if($value > $_GPC['marketprice'] || $value < 0){
                            message('请输入正确折扣金额！');
                        }
                    }
                }else{
                    foreach ($_GPC['discounts2'] as $value) {
                        if($value > $_GPC['marketprice'] || $value < 0){
                            message('请输入正确折扣金额！');
                        }
                    } 
                }


            }
            if($_GPC['returntype']){
                foreach ($_GPC['returns'] as $value) {
                    if($value > $_GPC['marketprice'] || $value < 0){
                        message('请输入正确返现金额！');
                    }
                }
            }else{
                foreach ($_GPC['returns2'] as $value) {
                    if($value > $_GPC['marketprice'] || $value < 0){
                        message('请输入正确返现金额！');
                    }
                } 
            }
            $data = array(
                'uniacid' => intval($_W['uniacid']),
                'displayorder' => intval($_GPC['displayorder']),
                'title' => trim($_GPC['goodsname']),
                'pcate' => intval($_GPC['category']['parentid']),
                'ccate' => intval($_GPC['category']['childid']),
                'tcate' => intval($_GPC['category']['thirdid']),
                'pcate1' => intval($_GPC['category2']['parentid']),
                'ccate1' => intval($_GPC['category2']['childid']),
                'tcate1' => intval($_GPC['category2']['thirdid']),
                'thumb' => save_media($_GPC['thumb']),
                'type' => intval($_GPC['type']),
                'isrecommand' => intval($_GPC['isrecommand']),
                'ishot' => intval($_GPC['ishot']),
                'isnew' => intval($_GPC['isnew']),
                'isdiscount' => intval($_GPC['isdiscount']),
                'issendfree' => intval($_GPC['issendfree']),
                'isnodiscount' => intval($_GPC['isnodiscount']),
                'istime' => intval($_GPC['istime']),
                'timestart' => strtotime($_GPC['timestart']),
                'timeend' => strtotime($_GPC['timeend']),
                'description' => trim($_GPC['description']),
                'goodssn' => trim($_GPC['goodssn']),
                'unit' => trim($_GPC['unit']),
                'createtime' => TIMESTAMP,
                'total' => intval($_GPC['total']),
                'totalcnf' => intval($_GPC['totalcnf']),
                'marketprice' => $_GPC['marketprice'],
                'weight' => $_GPC['weight'],
                'costprice' => $_GPC['costprice'],
                'productprice' => trim($_GPC['productprice']),
                'productsn' => trim($_GPC['productsn']),
                'credit' => trim($_GPC['credit']),
                'maxbuy' => intval($_GPC['maxbuy']),
                'usermaxbuy' => intval($_GPC['usermaxbuy']),
                'hasoption' => intval($_GPC['hasoption']),
                'opt_switch' => intval($_GPC['opt_switch']),
                'sales' => intval($_GPC['sales']),
                'share_icon' => trim($_GPC['share_icon']),
                'share_title' => trim($_GPC['share_title']),
                'cash' => intval($_GPC['cash']),
                'status' => intval($_GPC['status']),
                'showlevels' => is_array($_GPC['showlevels']) ? implode(",", $_GPC['showlevels']) : '',
                'buylevels' => is_array($_GPC['buylevels']) ? implode(",", $_GPC['buylevels']) : '',
                'showgroups' => is_array($_GPC['showgroups']) ? implode(",", $_GPC['showgroups']) : '',
                'buygroups' => is_array($_GPC['buygroups']) ? implode(",", $_GPC['buygroups']) : '',
                'isverify' => intval($_GPC['isverify']),
                'isverifysend' => intval($_GPC['isverifysend']),
                'dispatchsend' => intval($_GPC['dispatchsend']),
                'storeids' => is_array($_GPC['storeids']) ? implode(',', $_GPC['storeids']) : '',
                'noticeopenid' => trim($_GPC['noticeopenid']),
                'noticetype' => is_array($_GPC['noticetype']) ? implode(",", $_GPC['noticetype']) : '',
                'needfollow' => intval($_GPC['needfollow']),
                'followurl' => trim($_GPC['followurl']),
                'followtip' => trim($_GPC['followtip']),
                'deduct' => $_GPC['deduct'],
                "deduct2" => $_GPC["deduct2"],
                'virtual' => intval($_GPC['type']) == 3 ? intval($_GPC['virtual']) : 0,
                'discounts' => is_array($_GPC['discounts']) ? json_encode($_GPC['discounts']) : "",
                'discounts2' => is_array($_GPC['discounts2']) ? json_encode($_GPC['discounts2']) : "",
                'discounttype' => $_GPC['discounttype'],
                'discountway' => $_GPC['discountway'],
                'returntype' => $_GPC['returntype'],
                'returns' => is_array($_GPC['returns']) ? json_encode($_GPC['returns']) : "",
                'returns2' => is_array($_GPC['returns2']) ? json_encode($_GPC['returns2']) : "",
                'detail_logo' => save_media($_GPC['detail_logo']),
                'detail_shopname' => trim($_GPC['detail_shopname']),
                'detail_totaltitle' => trim($_GPC['detail_totaltitle']),
                'detail_btntext1' => trim($_GPC['detail_btntext1']),
                'detail_btnurl1' => trim($_GPC['detail_btnurl1']),
                'detail_btntext2' => trim($_GPC['detail_btntext2']),
                'detail_btnurl2' => trim($_GPC['detail_btnurl2']),
                "ednum" => intval($_GPC["ednum"]),
                "edareas" => trim($_GPC["edareas"]),
                "edmoney" => trim($_GPC["edmoney"]),
                "redprice" => $_GPC["redprice"],//红包价格
                "isopenchannel" => intval($_GPC["isopenchannel"]),
                'goods_balance' =>  intval($_GPC['goods_balance']),
                'balance_with_store' => intval($_GPC['balance_with_store']),
                'plugin' => trim($_GPC["plugin"])

            );

            if (p('area')) {
                $data['pcate_area'] = intval($_GPC['category_area']['parentid']);
                $data['ccate_area'] = intval($_GPC['category_area']['childid']);
                $data['tcate_area'] = intval($_GPC['category_area']['thirdid']);
            }
            if (!empty($_GPC['bonusmoney'])) {
                $data['bonusmoney'] = $_GPC['bonusmoney'];
            }
            //判断是否安装供应商插件判断有没有供应商id 
            if (p('supplier')) {
                //todo,这个有问题吧?其他公众号管理员也可以选择供货商和是否上架的
                if ($perm_role == 1) {
                    $data['supplier_uid'] = $_W['uid'];
                    $data['status'] = 0;
                } else {
                    $data['supplier_uid'] = $_GPC['supplier_uid'];
                    $data['status'] = $_GPC['status'];
                }
            } else {
                $data['status'] = $_GPC['status'];
            }

            if (p('love')) {
                $data['love_money'] = $_GPC['love_money'];
            }
            if ($pluginreturn) {
                $data['isreturn'] = intval($_GPC['isreturn']);   //添加全返开关    1:开    0:关
                $data['isreturnqueue'] = intval($_GPC['isreturnqueue']);   //添加全返排列开关    1:开    0:关
                $data['return_appoint_amount'] = intval($_GPC['return_appoint_amount']); //全返分红金额
            }
            $ladders = array(); //阶梯价格数组
            if($isladder && !empty($_GPC['minimum']) && !empty($_GPC['maximum']) && !empty($_GPC['ladderprice'])) {
                // 开启阶梯价格插件 && get阶梯数据
                for ($i=0; $i < count($_GPC['minimum']); $i++) { 
                    $ladders[$i]['minimum'] = $_GPC['minimum'][$i];
                    $ladders[$i]['maximum'] = $_GPC['maximum'][$i];
                    $ladders[$i]['ladderprice'] = $_GPC['ladderprice'][$i];
                }
            }
            if ($pluginyunbi) {
                $data['isyunbi'] = intval($_GPC['isyunbi']);   //返虚拟币开关    1:开    0:关
                $data['yunbi_consumption'] = floatval($_GPC['yunbi_consumption']);  //虚拟币 返现比例 
                
                $data['yunbi_commission'] = floatval($_GPC['yunbi_commission']);  //虚拟币 上级获得比例 
                $data['yunbi_deduct'] = floatval($_GPC['yunbi_deduct']);  //虚拟币最高抵扣 
                //1开启强制使用云币，0关闭
                $data['isforceyunbi'] = intval($_GPC['isforceyunbi']);
                //是否使用保单
                $data['isdeclaration'] = intval($_GPC['isdeclaration']);
                $data['virtual_declaration'] = floatval($_GPC['virtual_declaration']);
            }
            if (p('hotel')) {
                $data['deposit'] = $_GPC["deposit"];//房间押金
                $data['print_id'] = $_GPC["print_id"];//房间押金
            }
            $cateset = m('common')->getSysset('shop');
            $pcates = array();
            $ccates = array();
            $tcates = array();
            if (is_array($_GPC['cates'])) {
                $postcates = $_GPC['cates'];
                foreach ($postcates as $pid) {
                    if ($cateset['catlevel'] == 3) {
                        $tcate = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $pid,
                                ':uniacid' => $_W['uniacid']
                            ));
                        $ccate = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $tcate['parentid'],
                                ':uniacid' => $_W['uniacid']
                            ));
                        $tcates[] = $tcate['id'];
                        $ccates[] = $ccate['id'];
                        $pcates[] = $ccate['parentid'];
                    } else {
                        $ccate = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $pid,
                                ':uniacid' => $_W['uniacid']
                            ));
                        $ccates[] = $ccate['id'];
                        $pcates[] = $ccate['parentid'];
                    }
                }
            }
            $pcates2 = array();
            $ccates2 = array();
            $tcates2 = array();
            if (is_array($_GPC['cates2'])) {
                $postcates2 = $_GPC['cates2'];
                foreach ($postcates2 as $pid) {
                    if ($cateset['catlevel'] == 3) {
                        $tcate2 = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category2') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $pid,
                                ':uniacid' => $_W['uniacid']
                            ));
                        $ccate2 = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category2') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $tcate2['parentid'],
                                ':uniacid' => $_W['uniacid']
                            ));
                        $tcates2[] = $tcate2['id'];
                        $ccates2[] = $ccate2['id'];
                        $pcates2[] = $ccate2['parentid'];
                    } else {
                        $ccate2 = pdo_fetch('select id ,parentid from ' . tablename('sz_yi_category2') . ' where id=:id and uniacid=:uniacid limit 1',
                            array(
                                ':id' => $pid,
                                ':uniacid' => $_W['uniacid']
                            ));
                        $ccates2[] = $ccate2['id'];
                        $pcates2[] = $ccate2['parentid'];
                    }
                }
            }


            $data['pcates'] = implode(',', $pcates);
            $data['ccates'] = implode(',', $ccates);
            $data['tcates'] = implode(',', $tcates);
            $data['pcates2'] = implode(',', $pcates2);
            $data['ccates2'] = implode(',', $ccates2);
            $data['tcates2'] = implode(',', $tcates2);
            $content = htmlspecialchars_decode($_GPC['content']);
            preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]?))[\'|\"].*?[\/]?>/", $content,
                $imgs);
            $images = array();
            if (isset($imgs[1])) {
                foreach ($imgs[1] as $img) {
                    $im = array(
                        "old" => $img,
                        "new" => save_media($img)
                    );
                    $images[] = $im;
                }
            }
            foreach ($images as $img) {
                $content = str_replace($img['old'], $img['new'], $content);
            }
            $data['content'] = $content;
            if (p('commission')) {
                $cset = p('commission')->getSet();
                if (!empty($cset['level'])) {
                    $data['nocommission'] = intval($_GPC['nocommission']);
                    $data['nobonus'] = intval($_GPC['nobonus']);
                    $data['hascommission'] = intval($_GPC['hascommission']);
                    $data['hidecommission'] = intval($_GPC['hidecommission']);
                    $data['commission1_rate'] = $_GPC['commission1_rate'];
                    $data['commission2_rate'] = $_GPC['commission2_rate'];
                    $data['commission3_rate'] = $_GPC['commission3_rate'];
                    $data['commission1_pay'] = $_GPC['commission1_pay'];
                    $data['commission2_pay'] = $_GPC['commission2_pay'];
                    $data['commission3_pay'] = $_GPC['commission3_pay'];
                    $data['commission_thumb'] = save_media($_GPC['commission_thumb']);
                    $data['commission_level_id'] = intval($_GPC['commission_level_id']);
                }
            }
            if ($diyform_plugin) {
                $data["diyformtype"] = $_GPC["diyformtype"];
                $data["diyformid"] = $_GPC["diyformid"];
                $data["diymode"] = intval($_GPC["diymode"]);
            }
            $data["dispatchtype"] = intval($_GPC["dispatchtype"]);
            $data["dispatchprice"] = $_GPC["dispatchprice"];
            $data["dispatchid"] = $_GPC["dispatchid"];
            if ($data['total'] === -1) {
                $data['total'] = 0;
                $data['totalcnf'] = 2;
            }
            if (is_array($_GPC['thumbs'])) {
                $thumbs = $_GPC['thumbs'];
                $thumb_url = array();
                foreach ($thumbs as $th) {
                    $thumb_url[] = save_media($th);
                }
                $data['thumb_url'] = serialize($thumb_url);
            }
            if($_GPC['plugin'] == 'fund'){
                $data['productprice'] = $data['marketprice'];
            }
            if ($_GPC['plugin'] == 'recharge') {
                $data["province"] = $_GPC["reside"]['province'];
                $data["operator"] = intval($_GPC["operator"]);
            }
            if (empty($id)) {
                pdo_insert('sz_yi_goods', $data);
                $id = pdo_insertid();
                //判断是否安装酒店插件
                if (p('hotel')) {
                    if ($data['type'] == '99') { //当商品类型为房间时候
                        $room = array(
                            'title' => trim($_GPC['goodsname']),
                            'uniacid' => intval($_W['uniacid']),
                            'thumb' => trim($_GPC['thumb']),
                            'oprice' => trim($_GPC['marketprice']), //现价
                            'cprice' => trim($_GPC['productprice']),//原价
                            'deposit' => trim($_GPC['deposit']),
                            'goodsid' => $id,
                        );
                        pdo_insert('sz_yi_hotel_room', $room);
                    }
                }

                if($_GPC['plugin'] == 'fund'){
                    $fund_data = array(
                        'goodsid' => $id,
                        'uniacid' => intval($_W['uniacid']),
                        'allprice' => $_GPC['allprice'], 
                        'desc' => $_GPC['desc'], 
                        );
                    pdo_insert('sz_yi_fund_goods', $fund_data);
                }
                plog('shop.goods.add', "添加商品 ID: {$id}");
            } else {
                unset($data['createtime']);
                pdo_update('sz_yi_goods', $data, array(
                    'id' => $id
                ));
                if (p('hotel')) {
                    $rooms = pdo_fetch("select * from " . tablename('sz_yi_hotel_room') . " where goodsid=:goodsid and  uniacid=:uniacid",
                        array(
                            ":goodsid" => $id,
                            ":uniacid" => $_W['uniacid']
                        ));
                    $room = array(
                        'title' => trim($_GPC['goodsname']),
                        'uniacid' => intval($_W['uniacid']),
                        'thumb' => trim($_GPC['thumb']),
                        'oprice' => trim($_GPC['marketprice']),
                        'cprice' => trim($_GPC['productprice']),
                        'deposit' => trim($_GPC['deposit']),
                        'goodsid' => $id,
                    );
                    if ($data['type'] == '99') {
                        if (!empty($rooms)) {
                            pdo_update('sz_yi_hotel_room', $room, array(
                                'id' => $rooms['id']
                            ));
                        } else {
                            pdo_insert('sz_yi_hotel_room', $room);
                        }
                    } else {
                        if (!empty($rooms)) {
                            pdo_query("delete from " . tablename('sz_yi_hotel_room') . " where id={$rooms['id']}");
                        }
                    }
                }
                if($_GPC['plugin'] == 'fund'){
                    $fund_data = array(
                        'allprice' => $_GPC['allprice'], 
                        'desc' => $_GPC['desc'], 
                        );
                    pdo_update('sz_yi_fund_goods', $fund_data, array('goodsid' => $id));
                }
                plog('shop.goods.edit', "编辑{$lang['good']} ID: {$id}");
            }

            if (!empty($ladders)) {
                // 有阶梯数据 进行添加 编辑
                $ladder_data = array(
                        'uniacid'   => $_W['uniacid'],
                        'goodsid'   => $id,
                        'ladders'   => serialize($ladders),
                        'times'     => time()
                    );
                $ladderid = pdo_fetchcolumn("select id from " . tablename('sz_yi_goods_ladder') . " where goodsid=:goodsid and  uniacid=:uniacid",
                        array(
                            ":goodsid" => $id,
                            ":uniacid" => $_W['uniacid']
                        ));
                if(empty($ladderid)){
                    //添加阶梯价格
                    pdo_insert('sz_yi_goods_ladder', $ladder_data);
                } else {
                    //修改阶梯价格
                    pdo_update('sz_yi_goods_ladder', $ladder_data, array(
                            'id' => $ladderid
                        ));
                }

            }
            $totalstocks = 0;
            $param_ids = $_POST['param_id'];
            $param_titles = $_POST['param_title'];
            $param_values = $_POST['param_value'];
            $param_displayorders = $_POST['param_displayorder'];
            $len = count($param_ids);
            $paramids = array();
            for ($k = 0; $k < $len; $k++) {
                $param_id = "";
                $get_param_id = $param_ids[$k];
                $a = array(
                    "uniacid" => $_W['uniacid'],
                    "title" => $param_titles[$k],
                    "value" => $param_values[$k],
                    "displayorder" => $k,
                    "goodsid" => $id
                );
                if (!is_numeric($get_param_id)) {
                    pdo_insert("sz_yi_goods_param", $a);
                    $param_id = pdo_insertid();
                } else {
                    pdo_update('sz_yi_goods_param', $a, array(
                        'id' => $get_param_id
                    ));
                    $param_id = $get_param_id;
                }
                $paramids[] = $param_id;
            }
            if (count($paramids) > 0) {
                pdo_query("delete from " . tablename('sz_yi_goods_param') . " where goodsid=$id and id not in ( " . implode(',',
                        $paramids) . ")");
            } else {
                pdo_query('delete from ' . tablename('sz_yi_goods_param') . " where goodsid=$id");
            }
            $files = $_FILES;
            $spec_ids = $_POST['spec_id'];
            $spec_titles = $_POST['spec_title'];
            $specids = array();
            $len = count($spec_ids);
            $specids = array();
            $spec_items = array();
            for ($k = 0; $k < $len; $k++) {
                $spec_id = "";
                $get_spec_id = $spec_ids[$k];
                $a = array(
                    "uniacid" => $_W['uniacid'],
                    "goodsid" => $id,
                    "displayorder" => $k,
                    "title" => $spec_titles[$get_spec_id]
                );
                if (is_numeric($get_spec_id)) {
                    pdo_update("sz_yi_goods_spec", $a, array(
                        "id" => $get_spec_id
                    ));
                    $spec_id = $get_spec_id;
                } else {
                    pdo_insert('sz_yi_goods_spec', $a);
                    $spec_id = pdo_insertid();
                }
                $spec_item_ids = $_POST["spec_item_id_" . $get_spec_id];
                $spec_item_titles = $_POST["spec_item_title_" . $get_spec_id];
                $spec_item_shows = $_POST["spec_item_show_" . $get_spec_id];
                $spec_item_thumbs = $_POST["spec_item_thumb_" . $get_spec_id];
                $spec_item_oldthumbs = $_POST["spec_item_oldthumb_" . $get_spec_id];
                $spec_item_virtuals = $_POST["spec_item_virtual_" . $get_spec_id];
                $itemlen = count($spec_item_ids);
                $itemids = array();
                for ($n = 0; $n < $itemlen; $n++) {
                    $item_id = "";
                    $get_item_id = $spec_item_ids[$n];
                    $d = array(
                        "uniacid" => $_W['uniacid'],
                        "specid" => $spec_id,
                        "displayorder" => $n,
                        "title" => $spec_item_titles[$n],
                        "show" => $spec_item_shows[$n],
                        "thumb" => save_media($spec_item_thumbs[$n]),
                        "virtual" => $data['type'] == 3 ? $spec_item_virtuals[$n] : 0
                    );
                    $f = "spec_item_thumb_" . $get_item_id;
                    if (is_numeric($get_item_id)) {
                        pdo_update("sz_yi_goods_spec_item", $d, array(
                            "id" => $get_item_id
                        ));
                        $item_id = $get_item_id;
                    } else {
                        pdo_insert('sz_yi_goods_spec_item', $d);
                        $item_id = pdo_insertid();
                    }
                    $itemids[] = $item_id;
                    $d['get_id'] = $get_item_id;
                    $d['id'] = $item_id;
                    $spec_items[] = $d;
                }
                if (count($itemids) > 0) {
                    pdo_query("delete from " . tablename('sz_yi_goods_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id and id not in (" . implode(",",
                            $itemids) . ")");
                } else {
                    pdo_query('delete from ' . tablename('sz_yi_goods_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id");
                }
                pdo_update('sz_yi_goods_spec', array(
                    'content' => serialize($itemids)
                ), array(
                    "id" => $spec_id
                ));
                $specids[] = $spec_id;
            }
            if (count($specids) > 0) {
                pdo_query("delete from " . tablename('sz_yi_goods_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id and id not in (" . implode(",",
                        $specids) . ")");
            } else {
                pdo_query('delete from ' . tablename('sz_yi_goods_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id");
            }
            $option_idss = $_POST['option_ids'];
            $option_productprices = $_POST['option_productprice'];
            $option_marketprices = $_POST['option_marketprice'];
            $option_costprices = $_POST['option_costprice'];
            $option_stocks = $_POST['option_stock'];
            $option_weights = $_POST['option_weight'];
            $option_goodssns = $_POST['option_goodssn'];
            $option_productssns = $_POST['option_productsn'];
            $len = count($option_idss);
            $optionids = array();
            for ($k = 0; $k < $len; $k++) {
                $option_id = "";
                $ids = $option_idss[$k];
                $get_option_id = $_GPC['option_id_' . $ids][0];
                $idsarr = explode("_", $ids);
                $newids = array();
                foreach ($idsarr as $key => $ida) {
                    foreach ($spec_items as $it) {
                        if ($it['get_id'] == $ida) {
                            $newids[] = $it['id'];
                            break;
                        }
                    }
                }
                $newids = implode("_", $newids);
                $a = array(
                    "uniacid" => $_W['uniacid'],
                    "title" => $_GPC['option_title_' . $ids][0],
                    "productprice" => $_GPC['option_productprice_' . $ids][0],
                    "costprice" => $_GPC['option_costprice_' . $ids][0],
                    "marketprice" => $_GPC['option_marketprice_' . $ids][0],
                    "stock" => $_GPC['option_stock_' . $ids][0],
                    "weight" => $_GPC['option_weight_' . $ids][0],
                    "goodssn" => $_GPC['option_goodssn_' . $ids][0],
                    "productsn" => $_GPC['option_productsn_' . $ids][0],
                    "goodsid" => $id,
                    "specs" => $newids,
                    'virtual' => $data['type'] == 3 ? $_GPC['option_virtual_' . $ids][0] : 0,
                    "redprice" => $_GPC['option_redprice_' . $ids][0],
                );
                if($_GPC['plugin'] == 'fund'){
                    $a['productprice'] = $a['marketprice'];
                }
                $option_ladders = array(); //阶梯价格数组
                if($isladder && !empty($_GPC['option_minimum_'. $ids]) && !empty($_GPC['option_maximum_'. $ids]) && !empty($_GPC['option_ladderprice_'. $ids])) {
                    // 开启阶梯价格插件 && get阶梯数据
                    for ($i=0; $i < count($_GPC['option_minimum_'. $ids]); $i++) { 
                        $option_ladders[$i]['minimum']     = $_GPC['option_minimum_'. $ids][$i];
                        $option_ladders[$i]['maximum']     = $_GPC['option_maximum_'. $ids][$i];
                        $option_ladders[$i]['ladderprice'] = $_GPC['option_ladderprice_'. $ids][$i];
                    }
                    $a['option_ladders'] = serialize($option_ladders);
                }
                $totalstocks += $a['stock'];
                if (empty($get_option_id)) {
                    pdo_insert("sz_yi_goods_option", $a);
                    $option_id = pdo_insertid();
                } else {
                    pdo_update('sz_yi_goods_option', $a, array(
                        'id' => $get_option_id
                    ));
                    $option_id = $get_option_id;
                }
                $optionids[] = $option_id;
            }
            if (count($optionids) > 0) {
                pdo_query("delete from " . tablename('sz_yi_goods_option') . " where goodsid=$id and id not in ( " . implode(',',
                        $optionids) . ")");
            } else {
                pdo_query('delete from ' . tablename('sz_yi_goods_option') . " where goodsid=$id");
            }
            if ($data['type'] == 3 && $pv) {
                $pv->updateGoodsStock($id);
            } else {
                if (($totalstocks > 0) && ($data['totalcnf'] != 2)) {
                    pdo_update("sz_yi_goods", array(
                        "total" => $totalstocks
                    ), array(
                        "id" => $id
                    ));
                }
            }
            message($lang['good'].'更新成功！', $this->createWebUrl('shop/goods', array(
                'op' => 'post',
                'id' => $id,
                'plugin' => $_GPC['plugin']
            )), 'success');
        }
        if (p('commission')) {
            $com_set = p('commission')->getSet();
        }
        if ($pv) {
            $virtual_types = pdo_fetchall("select * from " . tablename('sz_yi_virtual_type') . " where uniacid=:uniacid order by id asc",
                array(
                    ":uniacid" => $_W['uniacid']
                ));
        }
        $levels = m('member')->getLevels();
        $details = pdo_fetchall('select detail_logo,detail_shopname,detail_btntext1, detail_btnurl1 ,detail_btntext2,detail_btnurl2,detail_totaltitle from ' . tablename('sz_yi_goods') . " where uniacid=:uniacid and detail_shopname<>''",
            array(
                ':uniacid' => $_W['uniacid']
            ));
        foreach ($details as &$d) {
            $d['detail_logo_url'] = tomedia($d['detail_logo']);
        }
        unset($d);
        $areas = m("cache")->getArray("areas", "global");
        if ($diyform_plugin) {
            $form_list = $diyform_plugin->getDiyformList();
        }
        if (!is_array($areas)) {
            require_once SZ_YI_INC . "json/xml2json.php";
            $file = IA_ROOT . "/addons/sz_yi/static/js/dist/area/Area.xml";
            $content = file_get_contents($file);
            $json = xml2json::transformXmlStringToJson($content);
            $areas = json_decode($json, true);
            m("cache")->set("areas", $areas, "global");
        }
    } elseif ($operation == 'display') {
        ca('shop.goods.view');
        if (!empty($_GPC['displayorder'])) {
            ca('shop.goods.edit');
            foreach ($_GPC['displayorder'] as $id => $displayorder) {
                pdo_update('sz_yi_goods', array(
                    'displayorder' => $displayorder
                ), array(
                    'id' => $id
                ));
            }
            plog('shop.goods.edit', '批量修改商品排序');
            message($lang['good'].'排序更新成功！', $this->createWebUrl('shop/goods', array(
                'op' => 'display'
            )), 'success');
        }
        if($_GPC['plugin'] == "fund"){
            p("fund")->autogoods();
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = ' WHERE `uniacid` = :uniacid AND `deleted` = :deleted';
        $params = array(
            ':uniacid' => $_W['uniacid'],
            ':deleted' => '0'
        );
        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' AND `title` LIKE :title';
            $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
        }


        if (!empty($_GPC['category']['thirdid'])) {
            $condition .= ' AND (`tcate` = :tcate or tcates = :tcate)';
            $params[':tcate'] = intval($_GPC['category']['thirdid']);
        }
        if (!empty($_GPC['category']['childid'])) {
            $condition .= ' AND (`ccate` = :ccate or ccates = :ccate)';
            $params[':ccate'] = intval($_GPC['category']['childid']);
        }
        if (!empty($_GPC['category']['parentid'])) {
            $condition .= ' AND (`pcate` = :pcate or pcates = :pcate)';
            $params[':pcate'] = intval($_GPC['category']['parentid']);
        }


        if (!empty($_GPC['category2']['thirdid'])) {
            $condition .= ' AND (`tcate1` = :tcate2 or tcates2 = :tcate2)';
            $params[':tcate2'] = intval($_GPC['category2']['thirdid']);
        }
        if (!empty($_GPC['category2']['childid'])) {
            $condition .= ' AND (`ccate1` = :ccate2 or ccates2 = :ccate2)';
            $params[':ccate2'] = intval($_GPC['category2']['childid']);
        }
        if (!empty($_GPC['category2']['parentid'])) {
            $condition .= ' AND (`pcate1` = :pcate2 or pcates2 = :pcate2)';
            $params[':pcate2'] = intval($_GPC['category2']['parentid']);
        }


        if ($_GPC["status"] != '') {
            $condition .= ' AND `status` = :status';
            $params[':status'] = intval($_GPC['status']);
        }

        //增加商品属性搜索
        $product_attr_list = array(
            'isnew' => '新品',
            'ishot' => '热卖',
            'isrecommand' => '推荐',
            'isdiscount' => '促销',
            'issendfree' => '包邮',
            'istime' => '限时',
            'isnodiscount' => '不参与折扣'
        );

        $product_attr = $_GPC['product_attr'];

        if ($product_attr) {
            $condition .= ' AND (';
            foreach ($product_attr as $k => $p_attr) {
                if ($k == 0) {
                    $condition .= " `{$p_attr}` = 1";
                } else {
                    $condition .= " OR `{$p_attr}` = 1";
                }
            }
            $condition .= ' )';
        }

        //供应商搜索
        if (!empty($_GPC["supplier_uid"]) && $_GPC["supplier_uid"] != 9999) {
            $condition .= " AND `supplier_uid` = " . "$_GPC[supplier_uid]";
        }

        if ($_GPC["supplier_uid"] == 9999) {
            $condition .= ' AND `supplier_uid` = 0';
        }

        $condition .= " AND `plugin` = '".$_GPC["plugin"]."' ";

        if (p('supplier')) {
            $suproleid = pdo_fetchcolumn('select id from' . tablename('sz_yi_perm_role') . ' where status1 = 1');
            $userroleid = pdo_fetchcolumn('select roleid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',
                array(':uid' => $_W['uid'], ':uniacid' => $_W['uniacid']));

            //Author:RainYang Date:2016-04-09 Content:修改供应商判断条件,有可能上面两个id都是空的情况,照成商品不显示
            if ((!empty($userroleid)) && ($userroleid == $suproleid)) {
                $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' and supplier_uid=' . $_W['uid'] . ' ORDER BY `status` DESC, `displayorder` DESC,
                    `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
                $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition . ' and supplier_uid=' . $_W['uid'];
                $total = pdo_fetchcolumn($sqls, $params);
            } else {
                $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' ORDER BY `status` DESC, `displayorder` DESC,
                        `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
                $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition;
                $total = pdo_fetchcolumn($sqls, $params);
            }
        } else {
            $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' ORDER BY `status` DESC, `displayorder` DESC,
                    `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition;
            $total = pdo_fetchcolumn($sqls, $params);
        }
        $list = pdo_fetchall($sql, $params);
        if($_GPC['plugin'] == "fund"){
            foreach ($list as $key => &$value) {
                $allprice = pdo_fetchcolumn("select allprice from ". tablename('sz_yi_fund_goods') ." where goodsid=".$value['id']);
                $value['casesceu'] = ceil($allprice / $value['marketprice']) <= $value['salesreal'];
                if(!$value['casesceu']){
                    $value['allrefund'] = pdo_fetchcolumn("select allrefund from ". tablename('sz_yi_fund_goods') ." where goodsid=".$value['id']);
                }
                $yetprice = pdo_fetchcolumn("select sum(og.price) as yetprice from ". tablename('sz_yi_order_goods') ." og left join " . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status > 0 and og.goodsid=".$value['id']);
                $value['yetprice'] = number_format($yetprice, 2);
                $value['allprice'] = number_format($allprice, 2);
            }
        }
        unset($value);
        $pager = pagination($total, $pindex, $psize);
    } elseif ($operation == 'delete') {
        ca('shop.goods.delete');
        $id = intval($_GPC['id']);
        $row = pdo_fetch("SELECT id, title, thumb FROM " . tablename('sz_yi_goods') . " WHERE id = :id", array(
            ':id' => $id
        ));
        if (empty($row)) {
            message('抱歉，'.$lang['good'].'不存在或是已经被删除！');
        }
        pdo_update('sz_yi_goods', array(
            'deleted' => 1
        ), array(
            'id' => $id
        ));
        //安装芸众差价，删除房间类型商品同时删除房型表中的商品
        if (p('hotel')) {
            $rooms = pdo_fetch("select * from " . tablename('sz_yi_hotel_room') . " where goodsid=:goodsid and  uniacid=:uniacid",
                array(
                    ":goodsid" => $id,
                    ":uniacid" => $_W['uniacid']
                ));
            if (!empty($rooms)) {
                pdo_query("delete from " . tablename('sz_yi_hotel_room') . " where id={$rooms['id']}");
            }
        }
        plog('shop.goods.delete', "删除商品 ID: {$id} 标题: {$row['title']} ");
        message('删除成功！', referer(), 'success');
    } elseif ($operation == 'setgoodsproperty') {
        ca('shop.goods.edit');
        $id = intval($_GPC['id']);
        $type = $_GPC['type'];
        $data = intval($_GPC['data']);
        $allrefund = 0;
        if($_GPC['plugin'] == "fund"){
            $allrefund = pdo_fetchcolumn("SELECT allrefund FROM " . tablename('sz_yi_fund_goods') . " WHERE goodsid = :id", array(
                    ':id' => $id
            ));  
        }
        if($allrefund == 1 && $type == 0){
            die(json_encode(array(
                'result' => 0
            )));
        }   
        if (in_array($type, array(
            'new',
            'hot',
            'recommand',
            'discount',
            'time',
            'sendfree',
            'nodiscount'
        ))) {
            $data = ($data == 1 ? '0' : '1');
            pdo_update('sz_yi_goods', array(
                'is' . $type => $data
            ), array(
                "id" => $id,
                "uniacid" => $_W['uniacid']
            ));
            if ($type == 'new') {
                $typestr = "新品";
            } else {
                if ($type == 'hot') {
                    $typestr = "热卖";
                } else {
                    if ($type == 'recommand') {
                        $typestr = "推荐";
                    } else {
                        if ($type == 'discount') {
                            $typestr = "促销";
                        } else {
                            if ($type == 'time') {
                                $typestr = "限时卖";
                            } else {
                                if ($type == 'sendfree') {
                                    $typestr = "包邮";
                                } else {
                                    if ($type == 'nodiscount') {
                                        $typestr = "不参与折扣状态";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            plog('shop.goods.edit', "修改商品{$typestr}状态   ID: {$id}");
            die(json_encode(array(
                'result' => 1,
                'data' => $data
            )));
        }
        if (in_array($type, array(
            'status'
        ))) {
            $data = ($data == 1 ? '0' : '1');
            pdo_update('sz_yi_goods', array(
                $type => $data
            ), array(
                "id" => $id,
                "uniacid" => $_W['uniacid']
            ));
            plog('shop.goods.edit', "修改商品上下架状态   ID: {$id}");
            die(json_encode(array(
                'result' => 1,
                'data' => $data
            )));
        }
        if (in_array($type, array(
            'type'
        ))) {
            $data = ($data == 1 ? '2' : '1');
            pdo_update('sz_yi_goods', array(
                $type => $data
            ), array(
                "id" => $id,
                "uniacid" => $_W['uniacid']
            ));
            plog('shop.goods.edit', "修改商品类型   ID: {$id}");
            die(json_encode(array(
                'result' => 1,
                'data' => $data
            )));
        }

        die(json_encode(array(
            'result' => 0
        )));
    } elseif ($operation == 'copygoods') {
        $uniacid = $_W['uniacid'];
        $goodsid_old = intval($_GPC['id']);
        $goods = pdo_fetch('select * from ' . tablename('sz_yi_goods') . ' where id = ' . $goodsid_old . ' and uniacid=' . $uniacid);
        if (empty($goods)) {
            message('未找到此商品，商品复制失败!', $this->createWebUrl('shop/goods'), 'error');
        }
        unset($goods['id']);
        $turn = pdo_fetchall("SELECT id FROM " . tablename('sz_yi_goods') . " WHERE title like '%{$goods['title']}%' and uniacid=:uniacid and deleted=0",
            array('uniacid' => $uniacid));
        $turncount = count($turn);
        if ($turncount >= 1) {
            $goods['title'] = $goods['title'] . '___(' . $turncount . ')';
        }
        pdo_insert('sz_yi_goods', $goods);
        $goodsid = pdo_insertid();

        $goodsoption = pdo_fetchall('select * from ' . tablename('sz_yi_goods_option') . ' where goodsid = ' . $goodsid_old . ' and uniacid=' . $uniacid);
        if (!empty($goodsoption)) {
            foreach ($goodsoption as $value_option) {
                unset($value_option['id']);
                $value_option['goodsid'] = $goodsid;
                pdo_insert('sz_yi_goods_option', $value_option);
            }

        }


        $goodsparam = pdo_fetchall('select * from ' . tablename('sz_yi_goods_param') . ' where goodsid = ' . $goodsid_old . ' and uniacid=' . $uniacid);
        if (!empty($goodsparam)) {
            foreach ($goodsparam as $value_param) {
                unset($value_param['id']);
                $value_param['goodsid'] = $goodsid;
                pdo_insert('sz_yi_goods_param', $value_param);
            }
        }

        $goodsspec = pdo_fetchall('select * from ' . tablename('sz_yi_goods_spec') . ' where goodsid = ' . $goodsid_old . ' and uniacid=' . $uniacid);
        if (!empty($goodsspec)) {
            foreach ($goodsspec as $value_spec) {
                $goodsspec_item = pdo_fetchall('select * from ' . tablename('sz_yi_goods_spec_item') . ' where specid = ' . $value_spec['id'] . ' and uniacid=' . $uniacid);
                unset($value_spec['id']);
                $value_spec['goodsid'] = $goodsid;
                pdo_insert('sz_yi_goods_spec', $value_spec);
                $goodsspecid = pdo_insertid();

                foreach ($goodsspec_item as $v) {
                    $v['specid'] = $goodsspecid;
                    unset($v['id']);
                    pdo_insert('sz_yi_goods_spec_item', $v);
                }
            }
        }
        message('商品复制成功！', $this->createWebUrl('shop/goods'), 'success');
    }
}
load()->func('tpl');
include $this->template('web/shop/goods');
