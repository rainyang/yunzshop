<link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>


<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-1 control-label">{{$lang['shopinfo']}}</label>
	<div class="col-sm-9 col-xs-12 col-md-11">
							{!! tpl_ueditor('goods[content]', $goods['content']) !!}

	</div>
</div>

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