@extends("app")


@push("head")
@endpush
@section("content")

<div id="dashboard" xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml">

    <!--<div id="map" class="map"></div>
    -->

    <h2 v-text="trip.boat_name"></h2>

    <table class="summup_engines" style="min-width: 500px">
        <tr>
            <th v-text="trip.boat_name"></th>
            <th>Mechanical Health</th>
            <th>Engine Efficiency</th>
            <th>RPM</th>
        </tr>
        <tr  v-for="(e,i) in trip.engines">
            <td v-text="e.name"></td>
            <td v-if="lastData[e.id]!=undefined && lastData[e.id]['MechanicalHealth']!=undefined"  v-bind:style="{ 'background-color': colorizeProperty(lastData[e.id]['MechanicalHealth']['valueInPercent'])}" v-text="lastData[e.id]['MechanicalHealth']['valueInPercent']+'%'"></td>
            <td v-else v-bind:style="{ 'background-color': colorizeProperty(0.00)}">0%</td>
            <td v-if="lastData[e.id]!=undefined && lastData[e.id]['EngineEfficiency']!=undefined" v-bind:style="{ 'background-color': colorizeProperty(100-lastData[e.id]['EngineEfficiency']['valueInPercent'])}" v-text="lastData[e.id]['EngineEfficiency']['valueInPercent']+'%'"></td>
            <td v-else v-bind:style="{ 'background-color': colorizeProperty(0.00)}">0%</td>
            <td v-if="lastData[e.id]!=undefined && lastData[e.id]['ChannelSpeed']!=undefined" v-text="lastData[e.id]['ChannelSpeed']"></td>
            <td v-else>0.00</td>
        </tr>
    </table>

    <div class="md-layout-item md-size-20" style="margin-top: 20px">
        <md-button type="button" class="md-primary md-raised" v-on:click="seeDetails" >SEE DETAILS</md-button>
    </div>
</div>

@endsection

@push("endscripts")

    @php
        $drawers = [];
        foreach($trip["engines"] as $key=>$engine)
        {
            $drawers["engine_".$key] = ($key == 0);
        }

    @endphp
    <script>

        var dashboard = new Vue({
            el:"#dashboard",
            data:{
                map: null,
                tileLayer: null,
                layers: [
                    {
                        id: 0,
                        name: 'Points',
                        active: false,
                        features: [],
                    },
                ],
                trip:@json($trip),
                lastData:@json($lastData)

            },
            mounted() {
                //this.initMap();
            },

            methods:{
                seeDetails(){
                    window.location.href="/engines";
                },
                initMap(){
                    this.map = L.map('map').setView([38.63, -90.23], 12);
                    this.tileLayer = L.tileLayer(
                        'https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png',
                        {
                            maxZoom: 18,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, &copy; <a href="https://carto.com/attribution">CARTO</a>',
                        }
                    );
                    this.tileLayer.addTo(this.map);
                },
                colorizeProperty : function(v){
                    if(v>75) return "#ff0006";
                    if(v>25) return "#f38630";
                    return "#7bcb8b";
                }


            }
        })
    </script>

    <style>
        .map { height: 600px; }
    </style>

@endpush

