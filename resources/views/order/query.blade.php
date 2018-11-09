<div style='max-height:500px;overflow:auto;'>
    <table class="table table-hover">
        <tbody>
        @if (is_array($order))
            @foreach($order as $row)

                <tr>
                    <td><img src='{{$row['avatar']}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {{$row['title']}}</td>
                    <td>{{$row['goods_id']}}</td>
                    <td>{{$row['mobile']}}</td>
                    <td style="width:80px;">
                        <a href="javascript:;" onclick='select_member({{json_encode($row)}})'>选择</a>
                    </td>
                </tr>
            @endforeach
        @elseif (is_numeric($order))
            <tr>
                <td>总店</td>
                <td></td>
                <td></td>
                <td style="width:80px;">
                    <a href="javascript:;" onclick='select_member({{json_encode(['uid' => 0, 'title' => '总店'])}})'>选择</a>
                </td>
            </tr>
        @endif

        @if (count($order) <= 0)
        <tr>
            <td colspan='4' align='center'>未找到订单</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>

