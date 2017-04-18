<link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>

 <div id="modal-areas"  class="modal fade" tabindex="-1">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择区域</h3></div>
            <div class="modal-body"  >

                @foreach ($parents as $value)
				@if ($value['areaname'] == '请选择省份') {{--{php continue }--}} @endif
                <div class='province' data-parent-id="{{ $value['id'] }}">
                     <label class='checkbox-inline' >
                         <input type='checkbox' class='cityall' /> {{ $value['areaname'] }}
                         <span class="citycount" ></span>
                     </label>

                    <ul></ul>

                </div>
                @endforeach

            </div>
            <div class="modal-footer">
                <a href="javascript:;" id='btnSubmitArea' class="btn btn-success" data-dismiss="modal" aria-hidden="true">确定</a>
                <a href="javascript:;" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
     </div>
</div>
 <script language='javascript'>
    $(function(){

        $('.province').mouseover(function(){
            var _this = $(this);
            if(_this.find('ul').text().length == 0){
                $.get('{!! yzWebUrl("area.area.select-city") !!}', {
                    parent_id: $(this).data('parent-id')
                }, function(dat){
                    _this.find('ul').html(dat);
                });
            }
            _this.find('ul').show();

        }).mouseout(function(){
              $(this).find('ul').hide();
        });

        $('.cityall').click(function(){
            var checked = $(this).get(0).checked;
            var citys = $(this).parent().parent().find('.city');
            citys.each(function(){
                $(this).get(0).checked = checked;
            });
            var count = 0;
            if(checked){
                count =  $(this).parent().parent().find('.city:checked').length;
            }
            if(count>0){
               $(this).next().html("(" + count + ")")    ;
            }
            else{
                $(this).next().html("");
            }
        });
        $('.city').click(function(){
            var checked = $(this).get(0).checked;
            var cityall = $(this).parent().parent().parent().parent().find('.cityall');

            if(checked){
                cityall.get(0).checked = true;
            }
            var count = cityall.parent().parent().find('.city:checked').length;
            if(count>0){
               cityall.next().html("(" + count + ")")    ;
            }
            else{
                cityall.next().html("");
            }
        });

    });






</script>