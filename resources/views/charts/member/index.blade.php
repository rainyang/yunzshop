@extends('layouts.base')
@section('title', '会员统计')
@section('content')

    <div class="rightlist">

        @include('layouts.tabs')


        <div class='panel panel-default form-horizontal form'>
            <div class='panel-heading'>会员统计</div>
            <div class='panel-body'>
                <div id="mainChart" style="width: 1200px;height:400px;"></div>
                <script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var myChart = echarts.init(document.getElementById('mainChart'));

                    // 指定图表的配置项和数据
                    var option = {
                        title: {
                            text: 'ECharts 入门示例'
                        },
                        tooltip: {},
                        legend: {
                            data:['销量']
                        },
                        xAxis: {
                            data: ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"]
                        },
                        yAxis: {},
                        series: [{
                            name: '销量',
                            type: 'bar',
                            data: [5, 20, 36, 10, 10, 20]
                        }]
                    };

                    // 使用刚指定的配置项和数据显示图表。
                    myChart.setOption(option);
                </script>
            </div>
        </div>
    </div>




@endsection

