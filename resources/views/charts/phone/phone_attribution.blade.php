@extends('layouts.base')

@section('content')
@section('title', trans('手机归属地统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">手机归属地统计</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th><h5>省市</h5></th>
                            <th><h5>会员数量</h5></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($phone_data as $row)
                        <tr>
                            <td>{{ $row['province'] }}</td>
                            <td>{{ $row['count(id)'] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
@endsection
