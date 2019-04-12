<style>
    .form-horizontal .form-group{margin-right: -50px;}
    .col-sm-9{padding-right: 0;}
    .tm .btn { margin-bottom:5px;}

    .panel-heading{
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
    }
    .panel-default{
        color: #8c8c8c;
        border-color: #efefef;
    }
    .panel-default .panel-heading{
        background: #fdfdfd;
        border-color:#efefef;
    }
    .panel-primary{
        border-color: #efefef;
    }
    .panel-primary .panel-heading{
        background: #44abf7;
        border-color:#efefef;
        background-color: rgba(22, 161, 199, 0.82);
    }
    .panel-success .panel-heading{
        color:#fff;
        background: #54c952;
        border-color:#efefef;
    }
    .panel-info .panel-heading {
        color:#fff;
        background:#8987d7;
        border-color:#efefef;
    }
    .panel-body ~ .panel-heading {
        border-top: 1px solid #efefef;
    }
    .panel-danger .panel-heading {
        color:#fff;
        background: #eb6060;
        border-color:#efefef;
    }
    .panel-warning .panel-heading {
        color:#fff;
        background: #ffc000;
        border-color:#efefef;
    }

</style>


<div class="row">
    <div class="col-sm-8" style="padding-right: 50px;">

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板标题</label>
            <div class="col-sm-9 col-xs-12">
                <input type="text"  disabled="disabled"  name="list[id]" id="title_id" style= "display:none"  class="form-control" value="{{$id}}" data-rule-required='true'>
                <input type="text"  disabled="disabled" id="title" name="list[name]"  class="form-control" value="{{$title}}" placeholder="模版标题" data-rule-required='true' />
            </div>
        </div>
        @if(!isset($is_edit))
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                    @foreach($list as $row)
                        <a href=" {!! yzWebUrl('setting.small-program.get-template-key',['id' => $row['id'],'page'=>$page,'title' => $row["title"]]) !!}">
                            <button type="button" onclick='template({{ '"'.$row['id'].'"' }},{{ '"'.$row["title"].'"' }})' class="btn btn-default mylink-nav" value="{{$row['id']}}">{{$row['title']}}</button>
                        </a>
                    @endforeach
            </div>
            <br>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" ></label>
            <input type="text" value="{{$page}}" name="offset" style= "display:none">
            <a class="prev pager-nav"  href=" {!! yzWebUrl('setting.small-program.add',['page' => $page ,'objective'=>'prev']) !!}">上一页</a>
            <a class="next pager-nav" href="{!!  yzWebUrl('setting.small-program.add',['page' => $page,'objective'=>'next']) !!}">下一页</a>
        </div>
        @endif
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >已选择</label>
            <div class="col-sm-8 title" id="word" style='padding-right:0' >
                <div id="div">

                </div>
            </div>
            <br>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" ></label>
            <div class="col-sm-8 title" id="word" style='padding-right:0' >
                如：购买地点@{{keyword1.DATA}},最多只能选10个
            </div>
            <br>

        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >选择关键字</label>
            <div class="col-sm-8 title" style='padding-right:0' >
                @foreach($keyWord as $word)
                    <button type="button" onclick='templateWord({{ '"'.$word['keyword_id'].'"' }},{{ '"'.$word["name"].'"' }})' class="btn btn-default mylink-nav" value="{{$word['keyword_id']}}">{{$word['name']}}</button>
                @endforeach
            </div>

        </div>


        <div id="type-items"></div>
    </div>
</div>

<script language='javascript'>

    var kw = 1;
    var temps;
    var contents;
    var i= 1;
    restempoption();
    function template(id,name) {
        $("#title").val(name);

    }
    function templateWord(id,name){
        if(i <= 10) {
            i++
            var inp = document.createElement("input");
            inp.type = "button";
            inp.value = name;
            inp.id = id;
            inp.onclick = function() {
                del(id);
            };

            var inp_val = document.createElement("input");
            inp_val.type = "text";
            inp_val.name = "key_val["+i+"]"
            inp_val.value =  id +':'+ name ;
            inp_val.id = id+1;
            // inp_val.style="display:none"

            document.getElementById("div").appendChild(inp);
            document.getElementById("div").appendChild(inp_val);
        } else {
            alert("已选择10个")
        }
    }
    function del(id) {
        var flag = confirm("确认删除?");
        if(flag) {
            document.getElementById(id).remove();
            document.getElementById(id+1).remove();
        }
    }

    function page(eles, active) {
        var prev = document.getElementsByClassName("prev");
        var next =  document.getElementsByClassName("next");
        var nowPage = 0; //定义当前页，默认值为0；
        next[0].onclick = function () {
            alert(1312);
            nowPage=nowPage+20;
            toggle(nowPage);
        }
        //上一页
        prev[0].onclick=function(){
            if(nowPage >= 20){
                nowPage = nowPage-20;
            }else {
                nowPage = 0;
            }
            toggle(nowPage);
        }
    }

    function toggle() {
        $.post("{!! yzWebUrl('setting.small-program.add') !!}", {
            order_id: order_id,
            remark: remark,
            invoice: invoice,
        }, function (json) {
            var json = $.parseJSON(json);
            if (json.result == 1) {
                window.location.reload();
            }
        });
    }
    function selecttemp()
    {
        var tid = $("#selecttemplate").val();
        var temp;

        for(var i=0;i<temps.length;i++){
            if(temps[i].template_id == tid)
            {
                temp =temps[i];
                break;
            }
        }

        if(temp == null) {
            return;
        } else {
            contents = temp.contents;

            if(contents[0] != 'first' || contents[contents.length-1] != 'remark') {
                alert("此模板不可用!");
                return;
            }
            $("#example").html(temp.content);

            $(".example-div").show();
            $("#title").val(temp.title);
            $("#template_id").val(temp.template_id);

            $('.key_item').remove();

            setcontents(0);
        }
    }


    function setcontents(i){

        if(contents.length == i) {
            return;
        }
        if(contents[i]!='first'&&contents[i]!='remark') {
            var url = "{!! yzWebUrl('setting.diy-temp.tpl') !!}";
            $.ajax({
                "url": url,
                "data":{tpkw:contents[i]},
                success: function (html) {
                    $(".btn-add-type").button("reset");
                    $("#type-items").append(html);
                    i++
                    setcontents(i);
                }
            });

        } else {
            i++
            setcontents(i);
        }
    }



    function addtempoption() {
        var tempcode = $("#tempcode").val();
        var data = {
            templateidshort: tempcode
        };
        var url = "{!! yzWebUrl('setting.wechat-notice.addTmp') !!}";
        $.ajax({
            "url": url,
            "data": data,
            success: function (ret) {
                if (ret.result == 1) {
                    alert("加入成功");
                    location.reload();
                } else {
                    alert("加入失败,请检查模板数量是否达到上限(25个)以及模板编码是否输入正确!");
                }
            }
        });
    }

    function restempoption() {
        var url = "{!! yzWebUrl('setting.wechat-notice.returnJson') !!}";
        $.ajax({
            "url": url,
            success: function (ret) {
                if (typeof ret === "string") {
                    var ret = $.parseJSON(ret);
                }
                
                if (ret.result == 1) {
                    $("#selecttemplate option").remove();
                    temps = ret.data.tmp_list;
                    for(var i=0;i<temps.length;i++){
                        $("#selecttemplate").append("<option value='"+temps[i].template_id+"'>"+temps[i].title+"</option>");
                    }
                }
            }
        });
    }

    function addType() {
        $(".btn-add-type").button("loading");
        var url = "{!! yzWebUrl('setting.diy-temp.tpl') !!}";
        $.ajax({
            "url": url,
            "data":{kw:kw},
            success: function (html) {
                $(".btn-add-type").button("reset");
                $("#type-items").append(html);
            }
        });
        kw++;
    }

    $('.diy-notice').select2();

</script>
