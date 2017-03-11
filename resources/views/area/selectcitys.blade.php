@foreach ($citys as $city)
    <li>
        <label class='checkbox-inline'>
            <input type='checkbox' class='city' style='margin-top:8px;' city="{{ $city[areaname] }}" /> {{ $city[areaname] }}
        </label>
    </li>
@endforeach
