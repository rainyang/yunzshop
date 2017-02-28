<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/24
 * Time: 上午11:13
 */

namespace app\common\helpers;


class ImageHelper
{

    /**
     * 【表单控件】: 图片上传与选择控件
     * @param string $name 表单input名称
     * @param string $value 表单input值
     * @param string $default 默认显示的缩略图
     * @param array $options 图片上传配置信息
     * <pre>
     * 		$options['width'] = '';
     * 		$options['height'] = '';
     * 		$options['global'] = '';// 是否显示 global 目录（公共目录）
     * 		$options['extras'] = array(
     * 			&nbsp;'image'=> 缩略图img标签的自定义属性及属性值 ,
     * 			&nbsp;'text'=> input 标签的自定义属性及属性值
     * 		)
     * </pre>
     * @return string
     */
    public static function tplFormFieldImage($name, $value = '', $default = '', $options = array()) {
        if (empty($default)) {
            $default = './resource/images/nopic.jpg';
        }
        $val = $default;
        if (!empty($value)) {
            $val = tomedia($value);
        }
        if (!empty($options['global'])) {
            $options['global'] = true;
        } else {
            $options['global'] = false;
        }
        if (empty($options['class_extra'])) {
            $options['class_extra'] = '';
        }
        if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
            if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
                exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
            }
        }
        $options['direct'] = true;
        $options['multiple'] = false;
        if (isset($options['thumb'])) {
            $options['thumb'] = !empty($options['thumb']);
        }
        $options['fileSizeLimit'] = intval(\YunShop::app()->setting['upload']['image']['limit']) * 1024;
        $s = '';
        if (!defined('TPL_INIT_IMAGE')) {
            $s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().next().children();
					options = '.str_replace('"', '\'', json_encode($options)).';
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.attachment);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, options);
				});
			}
			function deleteImage(elm){
				require(["jquery"], function($){
					$(elm).prev().attr("src", "./resource/images/nopic.jpg");
					$(elm).parent().prev().find("input").val("");
				});
			}
		</script>';
            define('TPL_INIT_IMAGE', true);
        }

        $s .= '
		<div class="input-group ' . $options['class_extra'] . '">
			<input type="text" name="' . $name . '" value="' . $value . '"' . (isset($options['extras']['text']) ? $options['extras']['text'] : '') . ' class="form-control" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>
			</span>
		</div>
		<div class="input-group ' . $options['class_extra'] . '" style="margin-top:.5em;">
			<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" ' . (isset($options['extras']['image']) ? $options['extras']['image'] : '') . ' width="150" />
			<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
		</div>';
        return $s;
    }
}