{template 'web/_header'}
<div class="w1200 m0a">
    {template 'web/shop/tabs'}
    <div class="main rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品分类</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->

            <form   action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >

        <input type="hidden" name="id" class="form-control" value="{php echo $item->id}" />
        <div class="panel panel-default">
            <div class="panel-body">
                {if !empty($item)}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类连接(点击复制)</label>
                        <div class="col-sm-9 col-xs-12">
                            <p class='form-control-static'>
                                <a href='javascript:;' title='点击复制连接' id='cp'>
                                    {if empty($parent)}
                                        {php echo $this->createMobileUrl('goods.category.index', ['pcate'=>$item->id])}
                                    {else}
                                        {if empty($parent1)}
                                            {php echo $this->createMobileUrl('goods.category.index', ['ccate'=>$item->id])}
                                        {else}
                                            {php echo $this->createMobileUrl('goods.category.index', ['tcate'=>$item->id])}
                                        {/if}
                                    {/if}
                                </a>
                            </p>
                        </div>
                    </div>
                {/if}

                {if !empty($parent)}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
                        <div class="col-sm-9 col-xs-12 control-label" style="text-align:left;">
                            {if !empty($parent)}{php echo $parent->name}  {/if}
                        </div>
                    </div>
                {/if}

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                            <input type="text" name="category[display_order]" class="form-control" value="{php echo $item->display_order}" />
                        {else}
                            <div class='form-control-static'>{php echo $item->display_order}</div>
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>分类名称</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                            <input type="text" name="category[name]" class="form-control" value="{php echo $item->name}" />
                        {else}
                            <div class='form-control-static'>{php echo $item->name}</div>
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类图片</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                            {php echo app\common\helpers\ImageHelper::tplFormFieldImage('category[thumb]', $item->thumb)}
                            <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
                        {else}
                            {if !empty($item['thumb'])}
                                <a href='{php echo tomedia($item->thumb)}' target='_blank'>
                                    <img src="{php echo tomedia($item->thumb)}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                </a>
                            {/if}
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类描述</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                            <textarea name="category[description]" class="form-control" cols="70">{php echo $item->description}</textarea>
                        {else}
                            <div class='form-control-static'>{php echo $item->description}</div>
                        {/if}

                    </div>
                </div>
                {if $level<=2}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">移动端分类广告</label>
                        <div class="col-sm-9 col-xs-12">
                            {ife 'shop.category' $item}
                                {php echo app\common\helpers\ImageHelper::tplFormFieldImage('category[adv_img]', $item->adv_img)}
                                <span class="help-block">建议尺寸: 640*320</span>
                            {else}
                                {if !empty($item['advimg'])}
                                    <a href='{php echo tomedia($item->adv_img)}' target='_blank'>
                                        <img src="{php echo tomedia($item->adv_img)}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                {/if}
                            {/if}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类广告链接</label>
                        <div class="col-sm-9 col-xs-12">
                            {ife 'shop.category' $item}
                                <div class="input-group ">
                                    <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{php echo $item->adv_url}" name="category[adv_url]">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                                        </span>
                                </div>
                            {else}
                                <div class='form-control-static'>{php echo $item->adv_url}</div>
                            {/if}
                        </div>
                    </div>

                {/if}

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                        <label class='radio-inline'>
                            <input type='radio' name='category[is_home]' value='1' {if $item->is_home==1}checked{/if} /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='category[is_home]' value='0' {if $item->is_home==0}checked{/if} /> 否
                        </label>
                        {else}
                        <div class='form-control-static'>{if empty($item->is_home)}否{else}是{/if}</div>
                        {/if}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        {ife 'shop.category' $item}
                        <label class='radio-inline'>
                            <input type='radio' name='category[enabled]' value='1' {if $item->enabled==1}checked{/if} /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='category[enabled]' value='0' {if $item->enabled==0}checked{/if} /> 否
                        </label>
                        {else}
                        <div class='form-control-static'>{if empty($item->enabled)}否{else}是{/if}</div>
                        {/if}
                    </div>
                </div>

                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="hidden" name="category[parent_id]" class="form-control" value="{php echo $item->parent_id}" />
                        <input type="hidden" name="category[level]" class="form-control" value="{php echo $item->level}" />
                        {ife 'shop.category' $item}
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                        {/if}
                        <input type="button" name="back" onclick='history.back()' {ifp 'shop.category.add|shop.category.edit'}style='margin-left:10px;'{/if} value="返回列表" class="btn btn-default col-lg-1" />
                    </div>
                </div>

            </div>
        </div>

        </form>
    </div>
    {template 'web/_footer'}

