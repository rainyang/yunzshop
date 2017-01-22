<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;
load()->func('tpl');

$op     = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$tempdo = empty($_GPC['tempdo']) ? "" : $_GPC['tempdo'];
$pageid = empty($_GPC['pageid']) ? "" : $_GPC['pageid'];
$apido  = empty($_GPC['apido']) ? "" : $_GPC['apido'];

if ($op == 'display') {
    ca('designer.page.view');
    $page     = empty($_GPC['page']) ? "" : $_GPC['page'];
    $pindex   = max(1, intval($page));
    $psize    = 10;
    $kw       = empty($_GPC['keyword']) ? "" : $_GPC['keyword'];
    $pages    = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid and pagename LIKE :name " . "ORDER BY savetime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
        ':uniacid' => $_W['uniacid'],
        ':name' => "%{$kw}%"
    ));
    $pagesnum = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid " . "ORDER BY savetime DESC ", array(
        ':uniacid' => $_W['uniacid']
    ));
    $total    = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid and pagename LIKE :name " . "ORDER BY savetime DESC ", array(
        ':uniacid' => $_W['uniacid'],
        ':name' => "%{$kw}%"
    ));

    $pager    = pagination($total, $pindex, $psize);
} elseif ($op == 'post') {
    $menus     = pdo_fetchall("SELECT id,menuname,isdefault FROM " . tablename('sz_yi_designer_menu') . " WHERE uniacid= :uniacid  ", array(
        ':uniacid' => $_W['uniacid']
    ));
    $pages     = pdo_fetchall("SELECT id,pagename,pagetype,setdefault FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid  ", array(
        ':uniacid' => $_W['uniacid']
    ));
    $categorys = pdo_fetchall("SELECT id,name,parentid FROM " . tablename('sz_yi_category') . " WHERE enabled=:enabled and uniacid= :uniacid and parentid= :parentid  ", array(
        ':uniacid' => $_W['uniacid'],
        ':enabled' => '1',
        ':parentid'=> '0'
    ));

    if (p('live')) {
        $domain = $_SERVER['HTTP_HOST'];
        $uniacid = $_W['uniacid'];

        //curl请求"获取直播间列表"的API
        load()->func('communication');
        $url = 'http://sy.yunzshop.com/shop_live.php?api=room&domain='.$domain.'&uniacid='.$uniacid;

        $result = ihttp_get($url);
        $result_array = json_decode($result['content'], true);
        $room_list = $result_array['data'];

        //获取sig
        if(!empty($openid)){
            $result_02 = ihttp_get('http://live.tbw365.cn/shop_live.php?api=IM/Get/sign&openid='.$openid);
            $result_02_array = json_decode($result_02['content'], true);
            $sig = $result_02_array['data']['sign'];
        }

    }


    if (!empty($pageid)) {
        ca('designer.page.edit');
        $datas = pdo_fetch("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid and id=:id", array(
            ':uniacid' => $_W['uniacid'],
            ':id' => $pageid
        ));
        $data  = htmlspecialchars_decode($datas['datas']);
        $data  = json_decode($data, true);
        if (!empty($data)) {
            foreach ($data as $i1 => &$dd) {
                if ($dd['temp'] == 'goods') {
                    foreach ($dd['data'] as $i2 => &$ddd) {
                        $goodinfo = pdo_fetchall("SELECT id,title,productprice,marketprice,thumb FROM " . tablename('sz_yi_goods') . " WHERE uniacid= :uniacid and id=:goodid", array(
                            ':uniacid' => $_W['uniacid'],
                            ':goodid' => $ddd['goodid']
                        ));
                        $goodinfo = set_medias($goodinfo, 'thumb');
                        if (!empty($goodinfo)) {
                            $data[$i1]['data'][$i2]['name']     = $goodinfo[0]['title'];
                            $data[$i1]['data'][$i2]['priceold'] = $goodinfo[0]['productprice'];
                            $data[$i1]['data'][$i2]['pricenow'] = $goodinfo[0]['marketprice'];
                            $data[$i1]['data'][$i2]['img']      = $goodinfo[0]['thumb'];
                        }
                    }
                    unset($ddd);
                } elseif ($dd['temp'] == 'richtext') {
                    $dd['content'] = $this->model->unescape($dd['content']);
                } elseif ($dd['temp'] == 'cube') {
                    $dd['params']['currentLayout']['isempty'] = true;
                    $dd['params']['selection']                = null;
                    $dd['params']['currentPos']               = null;
                    $has                                      = false;
                    $newarr                                   = new stdClass();
                    foreach ($dd['params']['layout'] as $k => $v) {
                        $arr = new stdClass();
                        foreach ($v as $kk => $vv) {
                            $arr->$kk = $vv;
                        }
                        $newarr->$k = $arr;
                    }
                    $dd['params']['layout'] = $newarr;
                }
            }
            $data = json_encode($data);
        }
        $data     = rtrim($data, "]");
        $data     = ltrim($data, "[");
        $pageinfo = htmlspecialchars_decode($datas['pageinfo']);
        $pageinfo = rtrim($pageinfo, "]");
        $pageinfo = ltrim($pageinfo, "[");
        $shopset  = m('common')->getSysset('shop');
        $system   = array(
            'shop' => array(
                'name' => $shopset['name'],
                'logo' => tomedia($shopset['logo'])
            )
        );
        $system   = json_encode($system);
    } else {
        ca('designer.page.edit');
        $defaultmenuid = $this->model->getDefaultMenuID();
        $pageinfo      = "{id:'M0000000000000',temp:'topbar',params:{title:'',desc:'',img:'',kw:'',footer:'1',footermenu:'{$defaultmenuid}', floatico:'0',floatstyle:'right',floatwidth:'40px',floattop:'100px',floatimg:'',floatlink:''}}";
    }
} elseif ($op == 'api') {
    if ($_W['ispost']) {
        if ($apido == 'savepage') {
            $id                    = $_GPC['pageid'];
            $datas                 = json_decode(htmlspecialchars_decode($_GPC['datas']), true);
            $date                  = date("Y-m-d H:i:s");
            $pagename              = $_GPC['pagename'];
            $pagetype              = $_GPC['pagetype'];
            $pageinfo              = $_GPC['pageinfo'];
            $p                     = htmlspecialchars_decode($pageinfo);
            $p                     = json_decode($p, true);
            $keyword               = empty($p[0]['params']['kw']) ? "" : $p[0]['params']['kw'];
            $p[0]['params']['img'] = save_media($p[0]['params']['img']);
            foreach ($datas as &$data) {
                if ($data['temp'] == 'banner' || $data['temp'] == 'menu' || $data['temp'] == 'picture') {
                    foreach ($data['data'] as &$d) {
                        $d['imgurl'] = save_media($d['imgurl']);
                    }
                    unset($d);
                } else if ($data['temp'] == 'shop') {
                    $data['params']['bgimg'] = save_media($data['params']['bgimg']);
                } else if ($data['temp'] == 'goods') {
                    foreach ($data['data'] as &$d) {
                        $d['img'] = save_media($d['img']);
                    }
                    unset($d);
                } else if ($data['temp'] == 'richtext') {
                    $content         = m('common')->html_images($this->model->unescape($data['content']));
                    $data['content'] = $this->model->escape($content);
                } else if ($data['temp'] == 'cube') {
                    foreach ($data['params']['layout'] as &$row) {
                        foreach ($row as &$col) {
                            $col['imgurl'] = save_media($col['imgurl']);
                        }
                        unset($col);
                    }
                    unset($row);
                }
            }
            unset($data);
            $insert = array(
                'pagename' => $pagename,
                'pagetype' => $pagetype,
                'pageinfo' => json_encode($p),
                'savetime' => $date,
                'datas' => json_encode($datas),
                'uniacid' => $_W['uniacid'],
                'keyword' => $keyword
            );
            if (empty($id)) {
                ca('designer.page.edit');
                $insert['createtime'] = $date;
                pdo_insert('sz_yi_designer', $insert);
                $id = pdo_insertid();
                plog('designer.page.edit', "店铺装修-添加修改页面 ID: {$id}");
            } else {
                ca('designer.page.edit');
                if ($pagetype == '4') {
                    $insert['setdefault'] = '0';
                }
                pdo_update('sz_yi_designer', $insert, array(
                    'id' => $id
                ));
                plog('designer.page.edit', "店铺装修-修改修改页面 ID: {$id}");
            }
            $rule = pdo_fetch("select * from " . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':name' => "sz_yi:designer:" . $id
            ));
            if (empty($rule)) {
                $rule_data = array(
                    'uniacid' => $_W['uniacid'],
                    'name' => 'sz_yi:designer:' . $id,
                    'module' => 'sz_yi',
                    'displayorder' => 0,
                    'status' => 1
                );
                pdo_insert('rule', $rule_data);
                $rid          = pdo_insertid();
                $keyword_data = array(
                    'uniacid' => $_W['uniacid'],
                    'rid' => $rid,
                    'module' => 'sz_yi',
                    'content' => trim($keyword),
                    'type' => 1,
                    'displayorder' => 0,
                    'status' => 1
                );
                pdo_insert('rule_keyword', $keyword_data);
            } else {
                pdo_update('rule_keyword', array(
                    'content' => trim($keyword)
                ), array(
                    'rid' => $rule['id']
                ));
            }
            echo $id;
            exit;
        } elseif ($apido == 'delpage') {
            ca('designer.page.delete');
            if (empty($pageid)) {
                message('删除失败！Url参数错误', $this->createPluginWebUrl('designer'), 'error');
            } else {
                $page = pdo_fetch("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid and id=:id", array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $pageid
                ));
                if (empty($page)) {
                    echo '删除失败！目标页面不存在！';
                    exit();
                } else {
                    $do = pdo_delete('sz_yi_designer', array(
                        'id' => $pageid
                    ));
                    if ($do) {
                        $rule = pdo_fetch("select * from " . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name  limit 1', array(
                            ':uniacid' => $_W['uniacid'],
                            ':module' => 'sz_yi',
                            ':name' => "sz_yi:designer:" . $pageid
                        ));
                        if (!empty($rule)) {
                            pdo_delete('rule_keyword', array(
                                'rid' => $rule['id']
                            ));
                            pdo_delete('rule', array(
                                'id' => $rule['id']
                            ));
                        }
                        plog('designer.page.edit', "店铺装修-修改修改页面 ID: {$pageid} 页面名称: {$page['pagename']}");
                        echo 'success';
                    } else {
                        echo '删除失败！';
                    }
                }
            }
        } elseif ($apido == 'selectgood') {
            $kw    = $_GPC['kw'];
            $goods = pdo_fetchall("SELECT id,title,productprice,marketprice,thumb,sales,unit FROM " . tablename('sz_yi_goods') . " WHERE uniacid= :uniacid and status=:status and deleted=0 AND plugin='' AND title LIKE :title ", array(
                ':title' => "%{$kw}%",
                ':uniacid' => $_W['uniacid'],
                ':status' => '1'
            ));
            $goods = set_medias($goods, 'thumb');
            echo json_encode($goods);
        } elseif ($apido == 'setdefault') {
            ca('designer.page.setdefault');
            $do   = $_GPC['d'];
            $id   = $_GPC['id'];
            $type = $_GPC['type'];
            if ($do == 'on') {
                $pages = pdo_fetch("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE pagetype=:pagetype and setdefault=:setdefault and uniacid=:uniacid ", array(
                    ':pagetype' => $type,
                    ':setdefault' => '1',
                    ':uniacid' => $_W['uniacid']
                ));
                if (!empty($pages)) {
                    $array = array(
                        'setdefault' => '0'
                    );
                    pdo_update('sz_yi_designer', $array, array(
                        'id' => $pages['id']
                    ));
                }
                $array  = array(
                    'setdefault' => '1'
                );
                $action = pdo_update('sz_yi_designer', $array, array(
                    'id' => $id
                ));
                if ($action) {
                    $json = array(
                        'result' => 'on',
                        'id' => $id,
                        'closeid' => $pages['id']
                    );
                    plog('designer.page.edit', "店铺装修-设置默认页面 ID: {$id} 页面名称: {$pages['pagename']}");
                    echo json_encode($json);
                }
            } else {
                $pages = pdo_fetch("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE  id=:id and uniacid=:uniacid ", array(
                    ':id' => $id,
                    ':uniacid' => $_W['uniacid']
                ));
                if ($pages['setdefault'] == 1) {
                    $array  = array(
                        'setdefault' => '0'
                    );
                    $action = pdo_update('sz_yi_designer', $array, array(
                        'id' => $pages['id']
                    ));
                    if ($action) {
                        $json = array(
                            'result' => 'off',
                            'id' => $pages['id']
                        );
                        plog('designer.page.edit', "店铺装修-关闭默认页面 ID: {$id} 页面名称: {$pages['pagename']}");
                        echo json_encode($json);
                    }
                }
            }
        } elseif ($apido == 'selectkeyword') {
            $kw   = $_GPC['kw'];
            $pid  = $_GPC['pid'];
            $rule = pdo_fetch("select * from " . tablename('rule_keyword') . ' where content=:content and uniacid=:uniacid and module=:module limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':content' => $kw
            ));
            if (empty($rule)) {
                echo 'ok';
            } else {
                $rule2 = pdo_fetch("select * from " . tablename('rule') . ' where id=:id and uniacid=:uniacid limit 1', array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $rule['rid']
                ));
                if ($rule2['name'] == 'sz_yi:designer:' . $pid) {
                    echo 'ok';
                }
            }
        } elseif ($apido == 'selectlink') {
            $type = $_GPC['type'];
            $kw   = $_GPC['kw'];
            if ($type == 'notice') {
                $notices = pdo_fetchall("select * from " . tablename('sz_yi_notice') . ' where title LIKE :title and status=:status and uniacid=:uniacid ', array(
                    ':uniacid' => $_W['uniacid'],
                    ':status' => '1',
                    ':title' => "%{$kw}%"
                ));
                echo json_encode($notices);
            } elseif ($type == 'good') {
                $goods = pdo_fetchall("select title,id,thumb,marketprice,productprice from " . tablename('sz_yi_goods') . ' where title LIKE :title and status=1 and deleted=0 and plugin=\'\' and uniacid=:uniacid ', array(
                    ':uniacid' => $_W['uniacid'],
                    ':title' => "%{$kw}%"
                ));
                $goods = set_medias($goods, 'thumb');
                echo json_encode($goods);
            } elseif ($type == 'article') {
                $articles = pdo_fetchall("select id,article_title from " . tablename('sz_yi_article') . ' where article_title LIKE :title and article_state=1 and uniacid=:uniacid ', array(
                    ':uniacid' => $_W['uniacid'],
                    ':title' => "%{$kw}%"
                ));
                echo json_encode($articles);
	    	} elseif ($type == 'coupon') {
	    		$articles = pdo_fetchall('select id,couponname,coupontype from ' . tablename('sz_yi_coupon') . ' where couponname LIKE :title and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':title' => "%{$kw}%"));
	    		echo json_encode($articles);
            } else {
                exit();
            }
        }
    }
    exit();
}elseif($op = 'iscategory')
{
    if($_GPC['level'] == '3'){
        $category = pdo_fetch("SELECT id,name,parentid FROM " . tablename('sz_yi_category') . " WHERE enabled=:enabled and uniacid= :uniacid and id= :id  ", array(
            ':uniacid' => $_W['uniacid'],
            ':enabled' => '1',
            ':id'=> $_GPC['categoryid']
        ));
        $c_level1 = $category['parentid'];
        $c_level2 = $_GPC['categoryid'];
    }else
    {
        $c_level1 = $_GPC['categoryid'];     
    }

    $categorys = pdo_fetchall("SELECT id,name,parentid FROM " . tablename('sz_yi_category') . " WHERE enabled=:enabled and uniacid= :uniacid and parentid= :parentid ", array(
            ':uniacid' => $_W['uniacid'],
            ':enabled' => 1,
            ':parentid'=> $_GPC['categoryid']
        ));

    $html = '';
    if($categorys)
    {
       foreach ($categorys as $key => $value) {
           $html.="<div class='fe-tab-link-line'>";

           
            if($_GPC['level'] == '3'){
                $html.="<div class='fe-tab-link-sub'><a class='chooseclick' href='javascript:;' data-id='{$value['id']}' ng-click='chooseLink(4, {$value['id']})'>选择</a></div>";
                $url = $this->createMobileUrl('shop/list',array('pcate'=>$c_level1,'ccate'=>$c_level2,'tcate'=>$value['id']));
                $html.="<div class='fe-tab-link-text' id='fe-tab-link-li-{$value['id']}' data-href='{$url}'>";
                $html.="<span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>";
            }else
            {
                $html.="<div class='fe-tab-link-sub'><a href='javascript:;' class='c_id' data-cid='{$value['id']}' >下一级 </a><a class='chooseclick' href='javascript:;' data-id='{$value['id']}' ng-click='chooseLink(4, {$value['id']})'>选择</a></div>";
                $url = $this->createMobileUrl('shop/list',array('pcate'=>$c_level1,'ccate'=>$value['id']));
                $html.="<div class='fe-tab-link-text' id='fe-tab-link-li-{$value['id']}' data-href='{$url}'>";
                $html.="<span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>";
            }
           $html.=$value['name'];
           $html.="</div>";
           $html.="</div>";
           $html .= "<div class='categorynull3 category3{$value['id']}'></div>";
        
        }   
        $data['success'] = 1;
        //$data['category_id'] = $c_level1;
        $data['html'] = $html;
        echo json_encode($data);
        exit;
        
    }else
    {
        $data['success'] = 0;
        $data['html'] = '没有发现分类数据！';
        echo json_encode($data);
        exit;
        
    }

}

include $this->template('index');
