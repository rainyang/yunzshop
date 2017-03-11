{template 'web/_header'}
<div class="w1200 m0a">
    {template 'web/shop/tabs'}
    <script language="javascript" src="../addons/sz_yi/static/js/dist/nestable/jquery.nestable.js"></script>
    <link rel="stylesheet" type="text/css" href="../addons/sz_yi/static/js/dist/nestable/nestable.css" />
    <style type='text/css'>
        .dd-handle { height: 40px; line-height: 30px}
    </style>
    <div class="main rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{php echo $this->createWebUrl('goods.category.index');}">商品分类 </a></li>
                {if !empty($parent)}
                <li>上级分类:{php echo $parent->name}</li>
                    <li class="active">

                        <a href="{php echo $this->createWebUrl('goods.category.index', ['parent_id'=>$parent['parent_id'], 'level'=>$parent['level']]);}">返回上一级 </a>
                    </li>
                {/if}
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="category">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <div class="dd" id="div_nestable">
                        <ol class="dd-list">
                            {loop $list $category}
                                <li class="dd-item" data-id="{$category['id']}">
                                    <div class="dd-handle"  style='width:100%;'>
                                        <img src="{php echo tomedia($category['thumb']);}" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp;
                                        [ID: {php echo $category['id'];}] {php echo $category['name'];}
                                            <span class="pull-right">
                                                {if $category['level'] < 3}
                                                    <a class='btn btn-default btn-sm' href="{php echo $this->createWebUrl('goods.category.index', ['parent_id'=>$category['id']]);}" title='子分类' >子分类</a>

                                                    {ifp 'shop.category.add'}
                                                        <a class='btn btn-default btn-sm' href="{php echo $this->createWebUrl('goods.category.add-category', ['parent_id'=>$category['id'], 'level'=>$category['level']+1]);}" title='添加子分类' ><i class="fa fa-plus"></i></a>

                                                    {/if}
                                                {/if}
                                                {ifp 'shop.category.edit|shop.category.view'}
                                                 <a class='btn btn-default btn-sm' href="{php echo $this->createWebUrl('goods.category.edit-category', ['id'=>$category['id']]);}" title="{ifp 'shop.category.edit'}修改{else}查看{/if}" >
                                                     <i class="fa fa-edit"></i>
                                                 </a>
                                                {/if}
                                                {ifp 'shop.category.delete'}
                                                    <a class='btn btn-default btn-sm' href="{php echo $this->createWebUrl('goods.category.deleted-category', ['id'=>$category['id']]);}" title='删除' onclick="return confirm('确认删除此分类吗？');return false;">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                {/if}
                                            </span>
                                    </div>
                                </li>
                            {/loop}
                        </ol>
{$pager}
                        <table class='table'>
                            <tr>
                                <td>
                                    {ifp 'shop.category.add'}
                                        <a href="{php echo $this->createWebUrl('goods.category.add-category');}" class="btn btn-primary"><i class="fa fa-plus"></i> 添加新分类</a>
                                    {/if}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


{template 'web/_footer'}