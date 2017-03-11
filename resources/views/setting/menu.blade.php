@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" type="text/css" href="../addons/sz_yi/static/js/dist/nestable/nestable.css">
    <script type="text/javascript" src="../addons/sz_yi/static/js/dist/nestable/jquery.nestable.js"></script>

    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{{yzWebUrl('menu.index')}}">菜单设置 </a></li>
            </ul>
        </div>

        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="category">
            <form action="" method="post">
                <div class="panel panel-default">
                    <div class="panel-body table-responsive">

                        <div class="dd" id="div_nestable">
                            <ol class="dd-list">
                            @foreach ($menus['data'] as $row)
                                @if (empty($row['parent_id']))
                                <li class="dd-item @if (count($row['childs'])>0) dd-collapsed @endif" data-id="{{$row['id']}}">
                                    <button data-action="collapse" type="button" style="display: none;">Collapse</button>
                                    <button data-action="expand" type="button" style="display: @if (count($row['childs'])>0) block @else none @endif;">Expand</button>
                                    <div class="dd-handle"  style='width:100%;'>
                                        [ID: {{$row['id']}}] {{$row['name']}}
                                        <span class="pull-right">
                                           <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.add', array('id'=>$row['id']))}}" title='添加子分类' ><i class="fa fa-plus"></i></a>

                                             <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.edit', array('id' => $row['id']))}}" title="查看" ><i class="fa fa-edit"></i></a>
                                            <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.del', array('id' => $row['id']))}}" title='删除' onclick="return confirm('确认删除此菜单吗？');return false;"><i class="fa fa-remove"></i></a>
                                        </span>
                                    </div>
                                </li>
                                @endif
                             @endforeach

                            </ol>
                            <table class='table'>
                                <tr>
                                    <td>
                                        <a href="{{yzWebUrl('menu.add')}}" class="btn btn-primary"><i class="fa fa-plus"></i> 添加新菜单</a>

                                        <input type="hidden" name="token" value="{$_W['token']}" />
                                        <input type="hidden" name="datas" value="" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
    </div>
    <script language='javascript'>



        $(function(){
            $(document).on('click', '.dd-item', function (e) {
                e.stopPropagation();

               var _this = $(this);

               if (_this.hasClass('dd-collapsed')) {
                   var json_url = "{!! yzWebUrl('menu.getJsonData') !!}";
                   var add_url = "{!! yzWebUrl('menu.add') !!}";
                   var edit_url = "{!! yzWebUrl('menu.edit') !!}";
                   var del_url = "{!! yzWebUrl('menu.del') !!}";

                   $.ajax({
                       url: json_url,
                       type: "get",
                       data: {id:$(this).data('id')},
                       dataType: "json",
                       success: function (json) {
                           if (json.result == 1) {
                               var objData = $.parseJSON(json.data);
                               if (objData.length > 0) {
                                   var html = '<ol class="dd-list" style="width:100%;">';
                                   for (var i = 0; i < objData.length; i++) {
                                       if (objData[i]['childs'].length > 0) {
                                           collapsed = 'dd-collapsed';
                                           show = 'block';
                                       } else {
                                           collapsed ='';
                                           show = 'none';
                                       }
                                       html += '<li class="dd-item ' + collapsed + ' " data-id="' + objData[i].id + '">';
                                       html += '<button data-action="collapse" type="button" style="display: none;">Collapse</button>';
                                       html += '<button data-action="expand" type="button" style="display: ' + show + ';">Expand</button>';
                                       html += '<div class="dd-handle">[ID: ' + objData[i].id + '] ' + objData[i].name;
                                       html += '<span class="pull-right">';
                                       html += '<a class="btn btn-default btn-sm" href="' + add_url + '&id=' + objData[i].id + '" title="添加子分类"><i class="fa fa-plus"></i></a>';
                                       html += '<a class="btn btn-default btn-sm" href="' + edit_url + '&id=' + objData[i].id + '" title="查看"><i class="fa fa-edit"></i></a>'
                                       html += '<a class="btn btn-default btn-sm" href="' + del_url + '&id=' + objData[i].id + '"  title="" onclick="return confirm(\'确认删除此菜单吗？\');return false;" data-original-title="删除"><i class="fa fa-remove"></i></a>';
                                       html += '</span></div></li>';
                                   }
                                   html += '</ol>';
                               }
                               _this.append(html);

                               _this.removeClass('dd-collapsed');
                               _this.find(' > button[data-action="collapse"]').css("display", "block");
                               _this.find(' > button[data-action="expand"]').css("display", "none");
                           }
                       }
                   });
               } else {
                   _this.addClass('dd-collapsed');
                   _this.find('button[data-action="collapse"]').css("display", "none");
                   _this.find('button[data-action="expand"]').css("display", "block");
                   _this.find('ol').remove();
               }

           });



        })
    </script>

    </div>
@endsection