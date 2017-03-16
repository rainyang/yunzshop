@extends('layouts.admin')

@section('content')

    <div class="w1200 m0a">

        <div class="rightlist">
            <form action="" method="get" class='form form-horizontal'>
                <div class="panel panel-info">
                    <div class="panel-heading">筛选</div>
                    <div class="panel-body">
                        <form action="./index.php" method="get" class="form-horizontal" role="form">
                            <input type="hidden" name="c" value="site" />
                            <input type="hidden" name="a" value="entry" />
                            <input type="hidden" name="m" value="sz_yi" />
                            <input type="hidden" name="do" value="plugin" />
                            <input type="hidden" name="p"  value="perm" />
                            <input type="hidden" name="method"  value="user" />
                            <input type="hidden" name="op" value="display" />
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <input class="form-control" name="keyword" id="" type="text" value="{$_GPC['keyword']}" placeholder="可搜索操作名帐号/姓名/手机号">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">角色</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <select name="roleid" class='form-control'>
                                        <option value="" {if $_GPC['status']==''} selected{/if}></option>
                                        <option value="" {if $_GPC['status']=='0'} selected{/if}>无角色</option>
                                        {loop $roles $role}
                                        <option value="{$role['id']}" {if $_GPC['roleid']== $role['id']} selected{/if}>{$role['rolename']}</option>
                                        {/loop}

                                    </select>  </div>

                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <select name="status" class='form-control'>
                                        <option value="" {if $_GPC['status']==''} selected{/if}></option>
                                        <option value="1" {if $_GPC['status'] == '1'} selected{/if}>启用</option>
                                        <option value="0" {if $_GPC['status'] == '0'} selected{/if}>禁用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"> </label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        操作员管理
                    </div>
                    <div class='panel-body'>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>登录ID</th>
                                <th>角色</th>
                                <th>姓名</th>
                                <th>手机</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {loop $list $row}
                            <tr>
                                <td>{$row['username']}</td>
                                <td>{php echo !empty($row['rolename'])?$row['rolename']:'无'}</td>
                                <td>{$row['realname']}</td>
                                <td>{$row['mobile']}</td>
                                <td>
                                    {if $row['status']==1}
                                    <span class='label label-success'>启用</span>
                                    {else}
                                    <span class='label label-danger'>禁用</span>
                                    {/if}
                                </td>
                                <td>
                                    {ifp 'perm.user.view|perm.user.edit'}<a class='btn btn-default' href="{php echo $this->createPluginWebUrl('perm/user', array('op' => 'post', 'id' => $row['id']))}"><i class="fa fa-edit"></i></a>{/if}
                                    {ifp 'perm.user.delete'}<a class='btn btn-default'  href="{php echo $this->createPluginWebUrl('perm/user', array('op' => 'delete', 'id' => $row['id']))}" onclick="return confirm('确认删除此操作员吗？');
                                    return false;"><i class="fa fa-remove"></i></a>{/if}

                                </td>
                            </tr>
                            {/loop}

                            </tbody>
                        </table>
                        {$pager}

                    </div>
                    {if 'perm.user.add'}
                    <div class='panel-footer'>
                        <a class='btn btn-primary' href="{{ yzWebUrl('user.user.store') }}"><i class="fa fa-plus"></i> 添加新操作员</a>
                    </div>
                    {/if}

                </div>
            </form>
        </div>
    </div>


    <script language='javascript'>

        function search_roles() {
            $("#module-menus1").html("正在搜索....")
            $.get('{php echo $this->createPluginWebUrl('perm/role',array('op'=>'query'));}', {
                keyword: $.trim($('#search-kwd1').val())
            }, function(dat){
                $('#module-menus1').html(dat);
            });
        }
        function select_role(o) {
            $("#roleid").val(o.id);
            $("#role").val( o.rolename );
            var perms = o.perms.split(',');
            $(':checkbox')
            $(':checkbox').removeAttr('disabled').removeAttr('checked').each(function(){

                var _this = $(this);
                var perm = '';
                if( _this.data('group') ){
                    perm+=_this.data('group');
                }
                if( _this.data('child') ){
                    perm+="." +_this.data('child');
                }
                if( _this.data('op') ){
                    perm+="." +_this.data('op');
                }
                if( $.arrayIndexOf(perms,perm)!=-1){
                    $(this).attr('disabled',true).get(0).checked =true;
                }

            });
            $(".close").click();
        }
    </script>


@endsection