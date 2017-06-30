
<div  class="panel panel-info">
    <ul class="add-shopnav" id="myTab">
        @foreach(Config::get('widget.withdraw') as $key=>$value)
            <li><a href="#{{$key}}">{{$value['title']}}</a></li>
        @endforeach

    </ul>
</div>