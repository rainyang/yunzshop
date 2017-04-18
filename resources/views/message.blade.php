@extends('layouts.base')

@section('content')
    <div class="jumbotron clearfix alert alert-{{$status}}">
        <div class="row">
            <div class="col-xs-12 col-sm-3 col-lg-2">
                <i class="fa fa-5x
                @if($status=='success') fa-check-circle @endif
                @if($status=='danger') fa-times-circle @endif
                @if($status=='info') fa-info-circle @endif
                @if($status=='warning') fa-exclamation-triangle @endif
                        "></i>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">


                <p>{!! $message !!}</p>

                @if($redirect)
                    <p><a href="{!! $redirect !!}">如果你的浏览器没有自动跳转，请点击此链接</a></p>
                    <script type="text/javascript">
                        setTimeout(function () {
                            location.href = "{!! $redirect !!}";
                        }, 3000);
                    </script>
                @else
                    <script type="text/javascript">
                      setTimeout(function () {
                        history.go(-1);
                      }, 3000);
                    </script>
                    <p>[<a href="javascript:history.go(-1);">点击这里返回上一页</a>] &nbsp; [<a href="{{yzWebUrl('index.index')}}">首页</a>]</p>
                @endif
            </div>
        </div>
    </div>
@endsection