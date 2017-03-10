@extends('layouts.admin')

@section('content')

        <form action="" method="get" class='form form-horizontal'>
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="sz_yi" />
                        <input type="hidden" name="do" value="plugin" />
                        <input type="hidden" name="p"  value="perm" />
                        <input type="hidden" name="method"  value="role" />
                        <input type="hidden" name="op" value="display" />
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="keyword" id="" type="text" value="{$_GPC['keyword']}" placeholder="可搜索角色名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <select name="status" class='form-control'>
                                    <option value="" {if $_GPC['status']==''} selected{/if}></option>
                                    <option value="1" {if $_GPC['status'] == '1'} selected{/if}>启用</option>
                                    <option value="0" {if $_GPC['status'] == '0'} selected{/if}>禁用</option>
                                </select>  </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">&nbsp;</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class='panel panel-default'>
                <div class='panel-heading'>
                    角色设置
                </div>
                <div class='panel-body'>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>角色名称</th>
                            <th>操作员数量</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {loop $list $row}
                        <tr>
                            <td>{$row['rolename']}</td>
                            <td>{$row['usercount']}</td>
                            <td>
                                {if $row['status']==1}
                                <span class='label label-success'>启用</span>
                                {else}
                                <span class='label label-danger'>禁用</span>
                                {/if}
                            </td>
                            <td>
                                {ifp 'perm.role.edit|perm.role.view'}<a class='btn btn-default' href="{php echo $this->createPluginWebUrl('perm/role', array('op' => 'post', 'id' => $row['id']))}"><i class="fa fa-edit"></i></a>{/if}
                                {ifp 'perm.role.delete'}<a class='btn btn-default'  href="{php echo $this->createPluginWebUrl('perm/role', array('op' => 'delete', 'id' => $row['id']))}" onclick="return confirm('确认删除此门店吗？');return false;"><i class="fa fa-remove"></i></a>{/if}
                            </td>

                        </tr>
                        {/loop}

                        </tbody>
                    </table>
                    {$pager}
                </div>
                {if 'perm.role.add'}
                <div class='panel-footer'>
                    <a class='btn btn-primary' href="{ $this->createWebUrl('member.memberlevel.update')}"><i class="fa fa-plus"></i> 添加新角色</a>
                </div>
                {/if}



@endsection