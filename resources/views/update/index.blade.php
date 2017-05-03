@extends('layouts.base')

@section('title','商城更新')

@section('content')

    <ul class="timeline">

    @foreach($list as $item)
        <!-- timeline time label -->
            <li class="time-label">
        <span class="bg-red">
            {{--{{date('Y-m-d',$item['create_at'])}}--}}
        </span>
            </li>
            <!-- /.timeline-label -->

            <!-- timeline item -->
            <li>
                <!-- timeline icon -->
                <i class="fa fa-clock-o bg-gray"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> {{--{{date('H:i',$item['create_at'])}}--}}</span>

                    <h3 class="timeline-header">版本：{{$item['version']}}</h3>

                    <div class="timeline-body">
                        <div class="form-group">{!! $item['description'] !!}</div>
                    </div>

                    <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs updateVersion" >更新版本</a>
                    </div>
                </div>
            </li>
            <!-- END timeline item -->
        @endforeach

    </ul>

    <script>

      $(".updateVersion").click(function() {
        var $btn = $(this);
        console.log($btn);
        $btn.button('loading');
        $.ajax({
          url: '{!! yzWebUrl('update.start-download') !!}',
          type: 'POST',
          dataType: 'json'
        })
          .done(function(json) {

            console.log("Downloading finished");
            console.log(json);
            $btn.button('reset');
          })
          .fail(function(message){
            console.log('update.start-download:',message)
          });

      });

    </script>
@endsection