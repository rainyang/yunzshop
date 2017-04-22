<tr>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
    </td>
    <td colspan="2">
        <input type="hidden" class="form-control" name="categoryids[]"  data-id="{$id}" data-name="categoryids" value="" placeholder="按钮名称" style="width:200px;float:left"  />
        <input class="form-control" type="text" data-id="{$id}" data-name="categorynames" placeholder="" value="" name="categorynames[]" style="width:200px;float:left">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link" type="button" data-id="{$id}" onclick="$('#modal-module-menus-categorys').modal();$(this).parent().parent().addClass('focuscategory')">选择分类</button>
        </span>
    </td>
</tr>
