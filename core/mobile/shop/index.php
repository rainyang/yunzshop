<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$designer  = p('designer');
$shopset   = m('common')->getSysset('shop');
$html = $shopset['footercontent'];
preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg]?))[\'|\"].*?[\/]?>/", $html, $imgs);
if (isset($imgs[1])) {
    foreach ($imgs[1] as $img) {
        $im       = array(
            "old" => $img,
            "new" => tomedia($img)
        );
        $images[] = $im;
    }
    if (isset($images)) {
        foreach ($images as $img) {
            $html = str_replace($img['old'], $img['new'], $html);
        }
    }
    $shopset['footercontent'] = $html;
}
if (empty($this->yzShopSet['ispc']) || isMobile()) {
    if ($designer) {
        $pagedata = $designer->getPage();
        if ($pagedata) {
            extract($pagedata);
            $guide = $designer->getGuide($system, $pageinfo);
            $_W['shopshare'] = array('title' => $share['title'], 'imgUrl' => $share['imgUrl'], 'desc' => $share['desc'], 'link' => $this->createMobileUrl('shop'));
            if (p('commission')) {
                $set = p('commission')->getSet();
                if (!empty($set['level'])) {
                    $member = m('member')->getMember($openid);
                    if (!empty($member) && $member['status'] == 1 && $member['isagent'] == 1) {
                        $_W['shopshare']['link'] = $this->createMobileUrl('shop', array('mid' => $member['id']));
                        if (empty($set['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) {
                            $trigger = true;
                        }
                    } elseif (!empty($_GPC['mid'])) {
                            $_W['shopshare']['link'] = $this->createMobileUrl('shop', array('mid' => $_GPC['mid']));
                    }
                }
            }
            include $this->template('shop/index_diy');
            exit;
        }
    }
}

if ($operation == 'index') {
	$advs = pdo_fetchall('select id,advname,link,thumb,thumb_pc from ' . tablename('sz_yi_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
	foreach($advs as $key => $adv){
		if(!empty($advs[$key]['thumb'])){
			$adv[] = $advs[$key];
		}
		if(!empty($advs[$key]['thumb_pc'])){
			$adv_pc[] = $advs[$key];
		}
	}
	$advs = set_medias($advs, 'thumb,thumb_pc');
	$advs_pc = set_medias($adv_pc, 'thumb,thumb_pc');
    $category = pdo_fetchall('select id,name,thumb,parentid,level from ' . tablename('sz_yi_category') . ' where uniacid=:uniacid and ishome=1 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
	$category = set_medias($category, 'thumb');
	//首页全部分类获取导航
	$categorylist = m('shop')->getCategory();
	foreach ($categorylist as $key1 => $value1) {
		if(is_array($value1['children']) && !empty($value1['children'])){
		 	foreach ($value1['children'] as $keyc => $valuec) {
		 	  	 $cgoodc = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where ccate=:ccate and tcate=:tcate and uniacid=:uniacid  and deleted = 0 limit 8",array(':ccate' => $valuec['id'] , ':tcate' => '' ,':uniacid' => $_W['uniacid'])) , 'thumb');
		 	     $categorylist[$key1]['children'][$keyc]['goods']= $cgoodc;		 		 
		 		if(is_array($valuec['children']) && !empty($valuec['children'])){
	 			 	foreach ($valuec['children'] as $keyt => $valuet) {
	 					$cgoodt = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where tcate=:tcate and ccate=:ccate and uniacid=:uniacid  and deleted = 0 limit 8",array(':ccate' => $valuec['id'] ,':tcate' =>$valuet['id'] , ':uniacid' => $_W['uniacid'])) , 'thumb');
						$categorylist[$key1]['children'][$keyc]['children'][$keyt]['goods']= $cgoodt;
	 			 	}
		 		}
		 	}
		}else{
			$goods= set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where pcate=:pcate and uniacid=:uniacid and ishot =1 and deleted = 0 limit 8",array(':pcate' => $value1['id'] , ':uniacid' => $_W['uniacid'])) , 'thumb');
			$categorylist[$key1]['goods'] = $goods;
		 }
	}
	//print_r($categorylist);exit;
	//echo 123;exit;
	$categoryfloor = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where parentid=0 and ishome=1 and enabled=1 and uniacid=".$_W['uniacid']),'advimg');
	//pc模板楼层分类获取
	foreach ($categoryfloor as $key => $value) {
		$children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where ishome=1 and parentid=:pid and uniacid=:uniacid",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
		$goods = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where pcate=:pcate and uniacid=:uniacid and ishot =1 and deleted = 0 limit 8",array(':pcate' => $value['id'] , ':uniacid' => $_W['uniacid'])) , 'thumb');
		$categoryfloor[$key]['goods'] = $goods;
		if(!empty($goods)){
			foreach ($goods as $keys => $values) {
				$thumb_url = unserialize($values['thumb_url']);
				$categoryfloor[$key]['thumb_url'][$values['id']] =$thumb_url;
			}
		}
		$categoryfloor[$key]['key'] = $key;
		foreach($children as $key1 => $value1){
			$categoryfloor[$key]['children'][$key1] = $value1;
			$third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and ishome=1 and uniacid=:uniacid",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
			$categoryfloor[$key]['third'] = $third;
		}
	}
	$index_name = array(
		'isrecommand' 	=> '精品推荐',
		'isnew' 		=> '新上商品',
		'ishot' 		=> '热卖商品',
		'isdiscount' 	=> '促销商品',
		'issendfree' 	=> '包邮商品',
		'istime' 		=> '限时特价'
		);
	foreach ($category as &$c) {
		$c['thumb'] = tomedia($c['thumb']);
		if ($c['level'] == 3) {
			$c['url'] = $this->createMobileUrl('shop/list', array('tcate' => $c['id']));
		} else if ($c['level'] == 2) {
			$c['url'] = $this->createMobileUrl('shop/list', array('ccate' => $c['id']));
		}
	}
	/*广告与商品*/
	//精品推荐
	$ads_pc = array();
	$goods_pc = array();
	if(!empty($this->yzShopSet['index']['isrecommand']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['isrecommand'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='isrecommand'", array(':uniacid' => $uniacid));
		$goods_pc['isrecommand'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and isrecommand=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['isrecommand'] = set_medias($ads_pc['isrecommand'], 'thumb');
		$goods_pc['isrecommand'] = set_medias($goods_pc['isrecommand'], 'thumb');
	}

	//新上商品
	if(!empty($this->yzShopSet['index']['isnew']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['isnew'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='isnew'", array(':uniacid' => $uniacid));
		$goods_pc['isnew'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and isnew=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['isnew'] = set_medias($ads_pc['isnew'], 'thumb');
		$goods_pc['isnew'] = set_medias($goods_pc['isnew'], 'thumb');
	}

	//热卖商品
	if(!empty($this->yzShopSet['index']['ishot']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['ishot'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='ishot'", array(':uniacid' => $uniacid));
		$goods_pc['ishot'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and ishot=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['ishot'] = set_medias($ads_pc['ishot'], 'thumb');
		$goods_pc['ishot'] = set_medias($goods_pc['ishot'], 'thumb');
	}

	//促销商品
	if(!empty($this->yzShopSet['index']['isdiscount']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['isdiscount'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='isdiscount'", array(':uniacid' => $uniacid));
		$goods_pc['isdiscount'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and isdiscount=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['isdiscount'] = set_medias($ads_pc['isdiscount'], 'thumb');
		$goods_pc['isdiscount'] = set_medias($goods_pc['isdiscount'], 'thumb');
	}

	//包邮商品
	if(!empty($this->yzShopSet['index']['issendfree']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['issendfree'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='issendfree'", array(':uniacid' => $uniacid));
		$goods_pc['issendfree'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and issendfree=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['issendfree'] = set_medias($ads_pc['issendfree'], 'thumb');
		$goods_pc['issendfree'] = set_medias($goods_pc['issendfree'], 'thumb');
	}

	//限时特价
	if(!empty($this->yzShopSet['index']['istime']) && !empty($this->yzShopSet['ispc'])){
		$ads_pc['istime'] = pdo_fetchall('select * from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='istime'", array(':uniacid' => $uniacid));
		$goods_pc['istime'] = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 and istime=1 order by displayorder desc limit 4', array(':uniacid' => $uniacid));
		$ads_pc['istime'] = set_medias($ads_pc['istime'], 'thumb');
		$goods_pc['istime'] = set_medias($goods_pc['istime'], 'thumb');
	}
	$ads_pc['bottom_ad'] = pdo_fetch('select link,thumb from ' . tablename('sz_yi_adpc') . " where uniacid=:uniacid and location='bottom_ad'", array(':uniacid' => $uniacid));
	if(!empty($ads_pc['bottom_ad'])){
		$ads_pc['bottom_ad'] = set_medias($ads_pc['bottom_ad'], 'thumb');	
	}

	if (is_app()) {
		//最新消息
		$message = pdo_fetchall('select * from ' . tablename('sz_yi_message') . ' where  openid=:openid', array(':openid' => $openid));
		foreach ($message as $key => $value) {
			if($value['status']== '0'){
				$is_read='has';
			}
		}
	} else {
		$is_read = '';
	}


	unset($c);
} else if ($operation == 'goods') {
	$type = $_GPC['type'];
	$args = array('page' => $_GPC['page'], 'pagesize' => 6, 'isrecommand' => 1, 'order' => 'displayorder desc,createtime desc', 'by' => '');
	$goods = m('goods')->getList($args);
}
if ($_W['isajax']) {
	if ($operation == 'index') {

		show_json(1, array('set' => $set, 'advs' => $advs, 'category' => $category, 'is_read' => $is_read));
	} else if ($operation == 'goods') {
		$type = $_GPC['type'];
		show_json(1, array('goods' => $goods, 'pagesize' => $args['pagesize']));
	} else if ($operation == 'category'){

		$category = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where parentid=0 and uniacid=".$_W['uniacid']),'advimg');

		foreach ($category as $key => $value) {
			$children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
			foreach($children as $key1 => $value1){
				$category[$key]['children'][$key1] = $value1;
				$third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
				foreach($third as $key2 => $value2){
					$category[$key]['children'][$key1]['third'][$key2] = $value2;
				}
			}
		}

		show_json(1,array('category' => $category));
	} else if ($operation == 'category_recommend'){

		$category = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_category')." where ishome=1 and parentid=0 and uniacid=".$_W['uniacid']),'advimg_pc');

		foreach ($category as $key => $value) {
			$children = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where ishome=1 and parentid=:pid and uniacid=:uniacid",array(':pid' => $value['id'],':uniacid' => $_W["uniacid"])),'advimg');
			$goods = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where pcate=:pcate and uniacid=:uniacid and isrecommand =1 and deleted = 0 limit 8",array(':pcate' => $value['id'] , ':uniacid' => $_W['uniacid'])) , 'thumb');
			$category[$key]['goods'] = $goods;
			foreach($children as $key1 => $value1){
				$category[$key]['children'][$key1] = $value1;
				$third = set_medias(pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and ishome=1 and uniacid=:uniacid",array(':pid' => $value1['id'] , ':uniacid' => $_W["uniacid"])),'advimg');
				$category[$key]['third'] = $third;
				
			}
		}

		show_json(1,array('category' => $category));
	} else if ($operation == 'children_goods'){
		$id = $_GPC['id'];
		$aid = $_GPC['aid'];
		if($aid){
			$goods = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where pcate=:pcate and uniacid=:uniacid and isrecommand =1 and deleted = 0 limit 8",array(':pcate' => $aid , ':uniacid' => $_W['uniacid'])) , 'thumb');
			show_json(1,array('goods' => $goods));	
		}else{
			if(empty($id)){
				show_json(0);
			}
			$goods = set_medias(pdo_fetchall(" select * from ".tablename('sz_yi_goods')." where ccate=:ccate and uniacid=:uniacid and deleted = 0",array(':ccate' => $id , ':uniacid' => $_W['uniacid'])) , 'thumb');
			$third = pdo_fetchall(" select  * from ".tablename('sz_yi_category')." where parentid=:pid and uniacid=:uniacid",array(':pid' => $id , ':uniacid' => $_W["uniacid"]));
			show_json(1,array('goods' => $goods,'third' => $third));
		}
	} elseif ($operation == 'property_goods'){
		$property = $_GPC['property'];
		$property_goods = set_medias(pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods')." WHERE deleted=0 and status=1 and uniacid=:uniacid and {$property}=1",array(':uniacid' => $_W['uniacid'])),'thumb');
		show_json(1,$property_goods);
	}
}

$this->setHeader();
include $this->template('shop/index');
