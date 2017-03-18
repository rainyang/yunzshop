@extends('layouts.admin')

@section('content')

@if($role['id'])
<form action="{{ yzWebUrl('user.role.update') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
    <input type="hidden" name='id' value="{{ $role['id'] or '' }}" />
@else
<form action="{{ yzWebUrl('user.role.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
@endif
    <div class='panel panel-default'>
        <div class='panel-heading'>
            角色设置
        </div>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 角色</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="YzRole[name]" class="form-control" value="{{ $role['name'] or '' }}" />
                    <!--div class='form-control-static'>{$item['rolename']}</div-->
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' name='YzRole[status]' value='1' @if($role['status'] == 1) checked @endif /> 启用
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='YzRole[status]' value='0' @if($role['status'] == 0) checked @endif /> 禁用
                    </label>
                    <span class="help-block">如果禁用，则当前角色的操作员全部会禁止使用</span>
                    <!--div class='form-control-static'>{if $item['status']==1}启用{else}禁用{/if}</div-->
                </div>
            </div>

            @include('user.permission.permission')

            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                    <input type="button" name="back" onclick='history.back()' style="margin-left: 10px" value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </div>
    </div>

</form>

@endsection