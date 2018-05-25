@extends('layouts.base')
@section('title', '会员关系基础设置')
@section('content')
    @include('layouts.tabs')
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">Banner</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('base[banner]', $banner)!!}
                            <span class='help-block'>长方型图片</span>
                        </div>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! tpl_ueditor('base[content]', $content) !!}

                        </div>
                    </div>

                </div>

                <div class='panel-heading'>
                    {{trans('通知设置')}}
                </div>
                <div class='panel-body'>
                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得推广权限通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text"  name="base[generalize_title]" class="form-control" value="{{$base['generalize_title']}}" ></input>
                            标题: 默认'获得推广权限通知'
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="base[generalize_msg]" class="form-control" >{{$base['generalize_msg']}}</textarea>
                            模板变量: [昵称] [时间]
                        </div>
                    </div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得推广权限通知</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name='base[member_agent]' class='form-control diy-notice'>
                                <option value="{{$base['member_agent']}}" @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_agent']))
                                selected @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($base['member_agent'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input class="mui-switch mui-switch-animbg" id="member_agent" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_agent']))
                               checked @endif
                               onclick="message_default(this.id)"/>
                    </div>

                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新增下线通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text"  name="base[agent_title]" class="form-control" value="{{$base['agent_title']}}" ></input>
                            标题: 默认'新增下线通知'
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="base[agent_msg]" class="form-control" >{{$base['agent_msg']}}</textarea>
                            模板变量: [昵称] [时间] [下级昵称]
                        </div>
                    </div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新增下线通知</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name='base[member_new_lower]' class='form-control diy-notice'>
                                <option value="{{$base['member_new_lower']}}" @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_new_lower']))
                                selected @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($base['member_new_lower'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input class="mui-switch mui-switch-animbg" id="member_new_lower" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_new_lower']))
                               checked @endif
                               onclick="message_default(this.id)"/>
                    </div>
                </div>

                <div class='panel-heading'>
                    {{trans('会员关系')}}
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示关系等级</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][]" value="1" @if (in_array(1, $relation_level)) checked @endif >1级</input>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][]" value="2" @if (in_array(2, $relation_level)) checked @endif >2级</input>
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][]"  value="3" @if (in_array(3, $relation_level)) checked @endif >3级</input>
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            $('.diy-notice').select2();
        </script>
        <script>
            function message_default(name) {
                var id = "#" + name;
                var setting_name = "relation_base";
                var select_name = "select[name='base[" + name + "]']"
                var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
                var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
                var postdata = {
                    notice_name: name,
                    setting_name: setting_name
                };
                if ($(id).is(':checked')) {
                    //开
                    $.post(url_open,postdata,function(data){
                        if (data) {
                            $(select_name).val(data.id)
                            showPopover($(id),"开启成功")
                        }
                    }, "json");
                } else {
                    //关
                    $.post(url_close,postdata,function(data){
                        $(select_name).val('');
                        showPopover($(id),"关闭成功")
                    }, "json");
                }
            }
            function showPopover(target, msg) {
                target.attr("data-original-title", msg);
                $('[data-toggle="tooltip"]').tooltip();
                target.tooltip('show');
                target.focus();
                //2秒后消失提示框
                setTimeout(function () {
                        target.attr("data-original-title", "");
                        target.tooltip('hide');
                    }, 2000
                );
            }
        </script>
    </section>@endsection