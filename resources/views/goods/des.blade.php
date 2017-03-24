<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$lang['shopinfo']}}</label>
	<div class="col-sm-9 col-xs-12">
							{!! app\common\helpers\UeditorHelper::tpl_ueditor('content', $goods['content']) !!}

	</div>
</div>
<style type="text/css">
.top_menu{
    top: 100px !important;
}
</style>
<script type="text/javascript">
  require(['bootstrap'], function ($) {
    $(document).scroll(function () {
      var toptype = $("#edui1_toolbarbox").css('position');
      if (toptype == "fixed") {
        $("#edui1_toolbarbox").addClass('top_menu');
      }
      else {
        $("#edui1_toolbarbox").removeClass('top_menu');
      }
    });
  });
</script>