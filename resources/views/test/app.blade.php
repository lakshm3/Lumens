<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:v-on="http://www.w3.org/1999/xhtml"
      xmlns:v-bind="http://www.w3.org/1999/xhtml">
<head>
    @isset($title) <title>{{ $title}}</title>@endisset
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{asset("libs/css/vue-material.min.css")}}">
    <link rel="stylesheet" href="{{asset("libs/css/default-dark.min.css")}}">

    <link href="{{asset("css/app.min.css")}}" rel="stylesheet">

    <script src="{{asset("libs/js/vue.js")}}"></script>
    <script src="{{asset("libs/js/vue-material.min.js")}}"></script>
    <script src="{{asset("libs/js/axios.min.js")}}"></script>
    <script src="{{asset("libs/js/chart.min.js")}}"></script>

    @stack("head")
</head>
<body>
<div id="app">
    <header>
        <i class="material-icons drawer-icon icon-button" v-on:click="toggleDrawer">
            menu
        </i>
        <h1>@isset($title) {{$title}} @else ENGINE @endisset</h1>

        <div class="right_buttons">
            @isset($trip)
                <i class="material-icons icon-button" v-on:click="goTo('{{route("trips.edit",$trip)}}')">
                    settings_applications
                </i>
            @endisset
        </div>

        @isset($trip)
            <div class="trip_info">
                Duration: <span v-text="current_time_trip"></span>
                <!--<md-button class="md-accent md-raised stopTrip" v-on:click="stopTrip()">Stop the monitoring</md-button> -->
            </div>
        @else
            <div class="trip_info">
               <md-button class="md-accent md-raised stopTrip" v-on:click="createTrip()">New monitoring</md-button>
            </div>
        @endisset
    </header>

    <div class="drawer" id="drawer-menu" v-bind:class="{ opened: drawerVisible}">

        <h2>MENU</h2>
        <i class="material-icons back icon-button" v-on:click="toggleDrawer">
            arrow_back
        </i>

            <div class="menu-items">
                @isset($trip)
                <a class="menu-item @if(isset($selected) && $selected == "dashboard") selected @endif " type="button" href="{{route("dashboard")}}">Dashboard</a>
                <a class="menu-item @if(isset($selected) && $selected == "engines") selected @endif " type="button" href="{{route("engines")}}">Engines</a>

                @else
                    <a class="menu-item @if(isset($selected) && $selected == "history") selected @endif " type="button" href="{{route("history")}}">History</a>
                @endisset
            </div>

        <div class="copyright">© Copyright Oryx - {{date("Y")}} </div>
    </div>


    <md-dialog-confirm
        :md-active.sync="stoptrip_dialog"
        md-title="Are you sure you want to stop the trip"
        md-content="You cannot revert it !"
        md-confirm-text="STOP THE TRIP"
        md-cancel-text="CANCEL"
        @md-cancel="onCancel"
        @md-confirm="onStopTripConfirm">

        <md-progress-bar md-mode="indeterminate" v-if="stoppingTrip"></md-progress-bar>

    </md-dialog-confirm>

</div>
<main>
    @yield("content")
</main>



<script>
    Vue.use(VueMaterial.default)
    window.axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }

    var app =  new Vue({
        el:"#app",
        data:{
            drawerVisible:false,
            trip:@isset($trip) @json($trip) @else {} @endisset,
            current_time_trip:"",
            stoptrip_dialog:false,
            stoppingTrip:false
        },

        mounted(){
            @isset($trip) setInterval(clockRunning, 10); @endisset

        },
        methods:{
            toggleDrawer:function () {
                this.drawerVisible = !this.drawerVisible;
            },
            goTo:function (url) {
                window.location.href = url;
            },
            stopTrip:function () {
                this.stoptrip_dialog = true;
            },
            createTrip:function () {
                window.location.href="/";
            },
            onStopTripConfirm:function () {
                axios.post("{{route("trips.index")}}_stop/"+this.trip.id).then(res =>{
                    this.stoppingTrip = false;
                    this.stoptrip_dialog = false;
                    window.location.href="/";
                }).catch(res =>{
                    this.stoppingTrip = false;
                })
            },
            onCancel:function () {
                this.stoptrip_dialog = false;

            }


        }
    });

    function clockRunning(){
        var startDate = new Date(app.trip.start_date);
        var currentTime = new Date()
        var current = new Date(currentTime - startDate).getTime();
        var d = parseInt (current /(24*3600*1000))

        var timeElapsed = new Date(currentTime - startDate)
        var date = d
        var hour = timeElapsed.getUTCHours()
        var min = timeElapsed.getUTCMinutes()
        var sec = timeElapsed.getUTCSeconds()
        var ms = timeElapsed.getUTCMilliseconds();

        app.current_time_trip =
            date + "d " +
            zeroPrefix(hour, 2) + "h" +
            zeroPrefix(min, 2) + "m" +
            zeroPrefix(sec, 2)
    };


    function zeroPrefix(num, digit) {
        var zero = '';
        for(var i = 0; i < digit; i++) {
            zero += '0';
        }
        return (zero + num).slice(-digit);
    }

</script>


@stack("endscripts")


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">


    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic|Material+Icons">
</body>
</html>
