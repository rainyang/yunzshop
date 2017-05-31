  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        Yun Shop
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2017 {{\Config::get('module.name')}}.</strong> All rights reserved.

  </footer>

  <script type="text/javascript">
      require(['bootstrap'],function(){
      });
  </script>
  @if(YunShop::app()->role == 'founder' && config('app.env') == 'production')
  <script type="text/javascript">
    var checkUrl = "{!! yzWebUrl('update.check') !!}";
    var todoUrl = "{!! yzWebUrl('update.index') !!}";
    function check_yun_shop_upgrade() {
      require(['util'], function (util) {
        if (util.cookie.get('check_yun_shop_upgrade')) {
          return;
        }
        console.log('check update');
        $.post(checkUrl, function (result) {
          if (result && result.msg == 'key or secret is null') {
              <?php redirect('setting.key.index');?>
                return;
          }
          if (result && result.updated != '0') {
             var html = '<div class="container" id="check_yun_shop_upgrade" style=" position: fixed;margin: auto;bottom: 0px;z-index: 999;">\
              <div class="row">\
              <div class="alert alert-danger">\
              <button type="button" class="close" data-dismiss="alert" onclick="check_yun_shop_upgrade_hide()" aria-hidden="true">×</button>\
            <h4><i class="icon fa fa-check"></i> 系统更新提示</h4>\
            商城检测到新版本:' + result.last_version + ' 请<a href="' + todoUrl + '"> 点击这里 </a> 更新到最新版本 \
              </div>\
              </div>\
              </div>';
            $('.main-footer').append(html);
          }
        });
      });
    }
    function check_yun_shop_upgrade_hide() {
      require(['util'], function (util) {
        util.cookie.set('check_yun_shop_upgrade', 1, 3600);
        $('#check_yun_shop_upgrade').remove();
      });
    }
    $(function () {
      check_yun_shop_upgrade();
    });
  </script>
  @endif