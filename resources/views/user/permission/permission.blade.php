<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">操作权限</label>
    <div class="col-sm-9 col-xs-12">
        <div class='panel panel-default'>
            <!-- 第一级-->
            @foreach($permissions as $keyOne=>$valueOne)
                @if(isset($valueOne['permit']) && $valueOne['permit'] === 1)
                    <div class='panel-heading'>
                        <label class='checkbox-inline'>
                            <input type='checkbox' name='perms[]' value='{{$keyOne}}' class='perm-all' {{in_array($keyOne, $rolePermission) ? 'disabled' : ''}}
                                   data-group='{{$keyOne}}' {{in_array($keyOne, $userPermissons) ? 'checked' : ''}} />
                            {{$valueOne['name'] or ''}}
                        </label>
                    </div>
                    <!-- 第二级-->
                    @if(isset($valueOne['child']))
                        <div class='panel-body perm-group'>
                            @foreach($valueOne['child'] as $keyTwo=>$valueTwo)
                                @if(isset($valueTwo['permit']) && $valueTwo['permit'] === 1)
                                    <span>
                                           <label class='checkbox-inline' style='width:100px;'>
                                               <input type='checkbox' name='perms[]' value='{{$keyTwo}}'
                                                      class='perm-all-item' data-group='{{$keyOne}}'
                                                      data-child='{{$keyTwo}}' {{in_array($keyTwo, $rolePermission) ? 'disabled' : ''}}
                                                       {{in_array($keyTwo, $userPermissons) ? 'checked' : ''}}
                                               /> <b> {{$valueTwo['name'] or ''}}</b>
                                           </label>
                                        <!-- 第三级-->
                                        @if(isset($valueTwo['child']))
                                            @foreach($valueTwo['child'] as $keyThird=>$valueThird)
                                                @if(isset($valueThird['permit']) && $valueThird['permit'] === 1)
                                                <label class="checkbox-inline">
                                                <input type="checkbox" name="perms[]" value="{{$keyThird}}"
                                                       class="perm-item" data-group="{{$keyOne}}" {{in_array($keyThird, $rolePermission) ? 'disabled' : ''}}
                                                       data-child="{{$keyTwo}}" data-op="{{$keyThird}}"
                                                        {{in_array($keyThird, $userPermissons) ? 'checked' : ''}}>
                                                    {{$valueThird['name'] or ''}}
                                            </label>
                                                @endif
                                            @endforeach
                                        @endif
                                    <!-- 第三级 end -->
                                        <br/>
                                     </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                <!-- 第二级end -->
                @endif
            @endforeach
        <!-- 第一级end -->

        </div>
    </div>
</div>
<script language="javascript">
  require(['bootstrap'], function ($) {

    $(function () {
      $('.perm-all').click(function () {
        var checked = $(this).get(0).checked;
        var group = $(this).data('group');
        $(".perm-item[data-group='" + group + "'],.perm-all-item[data-group='" + group + "']").each(function () {
          $(this).get(0).checked = checked;
        })
      })
      $('.perm-all-item').click(function () {
        var checked = $(this).get(0).checked;
        var group = $(this).data('group');
        var child = $(this).data('child');
        $(".perm-item[data-group='" + group + "'][data-child='" + child + "']").each(function () {
          $(this).get(0).checked = checked;
        })
      });
      $('.perm-item').click(function () {
        var group = $(this).data('group');
        var child = $(this).data('child');
        var check = false;
        $(this).closest('span').find(".perm-item").each(function () {
          if ($(this).get(0).checked) {
            check = true;
            return false;
          }
        });
        var allitem = $(".perm-all-item[data-group=" + group + "][data-child=" + child + "]");
        if (allitem.length == 1) {
          allitem.get(0).checked = check;
        }
        $(".perm-all[data-group=" + group + "]").get(0).checked = check;

      });


    })
      /*
       $(function () {
       $(':checkbox').attr('disabled', true);
       })
       */
  });
</script>