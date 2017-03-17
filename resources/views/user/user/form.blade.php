@extends('layouts.admin')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <div class="main">
                <form id="dataform" action="{{ yzWebUrl('user.user.store') }}" method="post" class="form-horizontal form" >
                    <input type="hidden" name="id" value="{$item['id']}" />
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            操作员设置
                        </div>

                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">角色</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <select name="widgets[role_id]" class='form-control'>
                                        <option value=""  selected>点击选择角色</option>
                                        @foreach($roleList as $role)
                                        <option value="{{  $role['id'] }}" >{{ $role['name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 操作员用户名</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="user[username]" class="form-control" value="{{ $user['username'] or '' }}" />
                                    <span class='help-block'>您可以直接输入系统已存在用户，且保证用户密码正确才能添加</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>  操作员密码</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="password" name="user[password]" class="form-control" value="{{ $user['password'] or '' }}" autocomplete="off" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"> 姓名</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="widgets[profile][realname]" class="form-control" value="{{ $user['realname'] or '' }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">电话</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="widgets[profile][mobile]" class="form-control" value="{{ $user['mobile'] or '' }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                                <div class="col-sm-9 col-xs-12">
                                    <label class='radio-inline'>
                                        <input type='radio' name='user[status]' value='2' @if($user['status'] == 2) checked @endif /> 启用
                                    </label>
                                    <label class='radio-inline'>
                                        <input type='radio' name='user[status]' value='1' @if($user['status'] == 1) checked @endif /> 禁用
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <span class='form-control-static'>用户可以在此角色权限的基础上附加其他权限</span>
                                </div>
                            </div>



                            @include('user.permission.permission')




                            <div class="form-group"></div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
                                    <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn btn-default" />
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="form-group col-sm-12">

                    </div>
                </form>
            </div>
        </div>


@endsection