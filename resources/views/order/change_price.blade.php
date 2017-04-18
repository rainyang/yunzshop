
<!-- 订单改价 -->
<div id="modal-changeprice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="form-horizontal form" action="{{yzWebUrl('test')}}" method="post" enctype="multipart/form-data">
        <input type='hidden' name='id' value="{{$item['id']}}" />
        <input type='hidden' name='op' value='deal' />
        <input type='hidden' id='changeprice-orderprice' value=""/>
        <input type='hidden' id='changeprice-dispatchprice' value=""/>
        <input type='hidden' name='to' value='confirmchangeprice' />
        <div class="modal-dialog"  style="width:750px;margin:0px auto;">
            <div class="modal-content" >
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>订单改价</h3>
                </div>
                <div class="modal-body">

                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-12">
                            <table class='table'>
                                <tr>
                                    <th style='width:30%;'>商品名称</th>
                                    <th style='width:15%;'>单价</th>
                                    <th style='width:10%;'>数量</th>
                                    <th style='width:20%;'>小计</th>
                                    <th style='width:10%;'>加价或减价</th>
                                    <th style='width:15%;'>运费</th>
                                </tr>
                                @foreach($order_goods_model as $order_goods)
                                    <tr>
                                        <td>{{$order_goods->hasOneGoods->title}}</td>
                                        <td class='realprice'>
                                            {{number_format($order_goods->price/$order_goods->total,2)}}
                                        </td>
                                        <td>{{$order_goods->total}}</td>
                                        <td>
                                            {{$order_goods->price}}
                                            {{--@if ($goods['realprice'] != $goods['oldprice'])--}}
                                            <label class='label label-danger'>改价</label>
                                            {{--@endif--}}
                                        </td>

                                        <td valign="top" >
                                            <input type='text' class='form-control changeprice_orderprice' name="changegoodsprice[{{$order_goods->id}}}]"  />
                                        </td>
                                        <td valign="top" rowspan='{{$order_goods->hasOneGoods->goods_sn[$order_goods->hasOneGoods->order_id]}}' style='vertical-align: top' >
                                            <input type='text' class='form-control'  value="{{$order_goods->hasOneGoodsDispatch?$order_goods->hasOneGoodsDispatch->dispatch_price:0.00}}" name='changedispatchprice' />
                                            <a href='javascript:;' onclick="$(this).prev().val('0');mc_calc()">直接免运费</a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan='2'></td>
                                    <td colspan='' style='color:green'>应收款</td>
                                    <td colspan='' style='color:green'>{{number_format($order_model->price)}}</td>
                                    <td colspan='2'  style='color:red'>改价后价格不能小于0元</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <div class="form-control-static">

                            </div>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-12">
                            <div class="form-control-static">

                                <b>购买者信息</b>  {{$order_model->address->address}} {{$order_model->address->realname}} {{$order_model->address->mobile}}<br/>
                                <b>买家实付</b>： <span id='orderprice'>{{$order_model->price-$order_model->dispatch_price}}</span> + <span id='dispatchprice'>{{$order_model->dispatch_price}}</span> <span id='changeprice'></span> = <span id='lastprice'>{{$order_model->price}}</span><br/>
                                <b>买家实付</b> = 原价 + 运费 + 涨价或减价<br/><br/>
                                <b><span style='color:red'>*</span>该订单最多支持99次改价，您已经修改 <span style='color:red'>{{$change_num}}</span> 次<br/>
                            </div>
                        </div>
                    </div>

                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2" name="confirmchange" value="yes" onclick='return mc_check()'>确认改价</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function mc_check(){
        var can = true;
        var lastprice = 0;
        $('.changeprice').each(function () {
            if( $.trim( $(this).val())==''){
                return true;
            }
            var p = 0;
            if ( !$.isNumber($(this).val())) {
                $(this).select();
                alert('请输入数字!');
                can =false;
                return false;
            }
            var val  = parseFloat( $(this).val() );
            if(val<=0 && Math.abs(val) > parseFloat( $(this).parent().prev().html())) {
                $(this).select();
                alert('单个商品价格不能优惠到负数!');
                can =false;
                return false;
            }
            lastprice+=val;
        });
        var op = order_price + dispatch_price+ lastprice;
        if( op <0){
            alert('订单价格不能小于0元!');
            return false;
        }
        if(!can){
            return false;
        }
        return true;
    }

    function mc_calc() {

        var change_dispatchprice = parseFloat($('#changeprice_dispatchprice').val());
        if(!$.isNumber($('#changeprice_dispatchprice').val())){
            change_dispatchprice = dispatch_price;
        }
        var dprice = change_dispatchprice;
        if (dprice <= 0) {
            dprice = 0;
        }
        $('#dispatchprice').html(dprice.toFixed(2));

        var oprice = 0;
        $('.changeprice_orderprice').each(function () {
            var p = 0;
            if ($.trim($(this).val()) != '') {
                p = parseFloat($.trim($(this).val()));
            }
            oprice += p;
        });
        if(Math.abs(oprice)>0){
            if (oprice < 0) {
                $('#changeprice').css('color', 'red');
                $('#changeprice').html( " - " + Math.abs(oprice));
            } else {
                $('#changeprice').css('color', 'green');
                $('#changeprice').html( " + " + Math.abs(oprice));
            }
        }
        var lastprice =  order_price + dprice + oprice;

        $('#lastprice').html( lastprice.toFixed(2) );

    }
</script>
