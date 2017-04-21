@extends('layouts.base')

@section('title','商城更新')

@section('content')
    <p>更新包大小：<span id="file-size">0</span> Bytes</p>
    <!-- 进度条 -->
    <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            <span id="imported-progress">0</span>%
        </div>
    </div>

    <script>
      // 更新进度条的函数
      function updateProgress(progress) {
        $('#imported-progress').html(progress);
        $('.progress-bar').css('width', progress+'%').attr('aria-valuenow', progress);
      }

      function downloadFile() {
        var file_size = 0;
        var progress  = 0;

        console.log("Prepared to download");

        $.ajax({
          url: '{!! yzWebUrl('update.prepare-download') !!}',
          type: 'GET',
          dataType: 'json',
          beforeSend: function() {
            $('#update-button').html('<i class="fa fa-spinner fa-spin"></i> 正在准备').prop('disabled', 'disabled');
          },
        })
          .done(function(json) {
            console.log(json);

            file_size = json.file_size;

            $('#file-size').html(file_size);

            // 显示进度条

            console.log("started downloading");
            $.ajax({
              url: '{!! yzWebUrl('update.start-download') !!}',
              type: 'POST',
              dataType: 'json'
            })
              .done(function(json) {
                // set progress to 100 when got the response
                progress = 100;

                console.log("Downloading finished");
                console.log(json);
              })
              .fail(showAjaxError);

            var interval_id = window.setInterval(function() {

              $('#imported-progress').html(progress);
              $('.progress-bar').css('width', progress+'%').attr('aria-valuenow', progress);

              if (progress == 100) {
                clearInterval(interval_id);

                // 到此远程文件下载完成，继续其他逻辑
              } else {
                $.ajax({
                  url: '{!! yzWebUrl('update.get-file-size') !!}',
                  type: 'GET'
                })
                  .done(function(json) {
                    progress = (json.size / file_size * 100).toFixed(2);

                    updateProgress(progress);

                    console.log("Progress: "+progress);
                  })
                  .fail(showAjaxError);
              }

            }, 300);

          })
          .fail(showAjaxError);

      }
    </script>
@endsection