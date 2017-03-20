@extends('layouts.base')

@section('content')

        <form action="" method="get" class='form form-horizontal'>
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action=" " method="get" class="form-horizontal" role="form">

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                            <div class="col-xs-12 col-sm-8 col-lg-9">
                                <input class="form-control" name="keyword" id="" type="text" value=" " placeholder="可搜索角色名称">
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

                        @foreach($roleList->items() as $role)
                        <tr>
                            <td> {{ $role->name }}</td>
                            <td>{{$role->roleUser->count()}}</td>
                            <td>
                                @if($role['status'] == 1)
                                <span class='label label-success'>启用</span>
                                @else
                                <span class='label label-danger'>禁用</span>
                                @endif
                            </td>
                            <td>
                                <a class='btn btn-default' href="{{ yzWebUrl('user.role.update', array('id' => $role->id)) }}"><i class="fa fa-edit"></i></a>
                                <a class='btn btn-default'  href="{{ yzWebUrl('user.role.destory', array('id' => $role->id)) }}" onclick="return confirm('确认删除该角色吗？');return false;"><i class="fa fa-remove"></i></a>
                            </td>

                        </tr>
                        @endforeach


                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
                <div class='panel-footer'>
                    <a class='btn btn-primary' href="{{ yzWebUrl('user.role.store') }}"><i class="fa fa-plus"></i> 添加新角色</a>
                </div>


@endsection