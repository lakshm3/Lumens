

@extends("app")

@section("content")

    <h2>History of  trips</h2>
    <table class="summup_engines" style="min-width: 500px">
        <tr>
            <th>Trip name</th>
            <th>Boat name</th>
            <th>Weight</th>
            <th>Nb Engines</th>
            <th>Start date</th>
            <th>Stop date</th>
            <th>In Progress</th>
            <th>Actions</th>
        </tr>


        @foreach($trips as $t)
            <tr>
                <td>{{$t->name}}</td>
                <td>{{$t->boat_name}}</td>
                <td>{{$t->weight}}</td>
                <td>{{count($t->engines)}}</td>
                <td>{{$t->start_date}}</td>
                <td>@isset($t->end_date) {{$t->end_date}} @else Not stopped @endif</td>
                <td>{{$t->in_progress?"In Progress":"Stopped"}}</td>
            </tr>

        @endforeach

    </table>

@endsection

@push("endscripts")
@endpush
