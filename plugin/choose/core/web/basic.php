<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;
load()->func('tpl');
$op     = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($op == 'display') {

  
           
        
        
    
    
} elseif($op == 'create'){
    $sql="select u.* from ".tablename('sz_yi_perm_user')." u left join " .tablename('sz_yi_perm_role'). " r on r.id = u.roleid where r.status1=1 and u.uniacid = :uniacid";
    $agent=pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid']));
    if(checksubmit('submit')){
        $date=date("Y-m-d H:i:s");
        $agentname=pdo_fetch('select username from ' .tablename('sz_yi_perm_user'). ' where uid=:uid and uniacid=:uniacid',array(':uid'=>$_GPC['uid'],':uniacid'=>$_W['uniacid']));
           pdo_insert('sz_yi_chooseagent',array(
           	'pagename'=>$_GPC['pagename'],
           	'isopen'=>$_GPC['openclose'],
           	'uid'=>$_GPC['uid'],
           	'createtime'=>$date,
           	'savetime'=>$date,
           	'agentname'=>$agentname['username'],
           	'uniacid'=>$_W['uniacid']

           	));
           message('快速选购页添加成功!', $this->createPluginWebUrl('choose'), 'success');
    }       
} elseif($op == 'change'){
	$sql="select u.* from ".tablename('sz_yi_perm_user')." u left join " .tablename('sz_yi_perm_role'). " r on r.id = u.roleid where r.status1=1 and u.uniacid = :uniacid";
    $agent=pdo_fetchall($sql,array(':uniacid'=>$_W['uniacid']));
    $open=pdo_fetch('select * from ' .tablename('sz_yi_chooseagent'). ' where id=' .$_GPC['pageid']);
    if(checksubmit('submit')){
        $date=date("Y-m-d H:i:s");
        
        if($_GPC['openclose']==1){
			$agentname=pdo_fetch('select username from ' .tablename('sz_yi_perm_user'). ' where uid=:uid and uniacid=:uniacid',array(':uid'=>$_GPC['uid'],':uniacid'=>$_W['uniacid']));
			pdo_update('sz_yi_chooseagent',array(
			'pagename'=>$_GPC['pagename'],	
           	'isopen'=>$_GPC['openclose'],
           	'uid'=>$_GPC['uid'],
           	'savetime'=>$date,
           	'agentname'=>$agentname['username'],

           	));
           message('快速选购页修改成功!',$this->createPluginWebUrl('choose'), 'success');
           
        }else{
			pdo_update('sz_yi_chooseagent',array(
			'pagename'=>$_GPC['pagename'],	
           	'isopen'=>$_GPC['openclose'],
           	'uid'=>'',
           	'savetime'=>$date,
           	'agentname'=>'未设置',

           	));
           	message('快速选购页修改成功!', $this->createPluginWebUrl('choose'), 'success');
        }
           
    }    

}
include $this->template('basic');
