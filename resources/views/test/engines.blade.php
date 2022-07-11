@extends("app")
@section("content")

<style>
[v-cloak] > * { display:none }
[v-cloak]::before { content: "loading…" }

</style>
<div id="engines" v-cloak xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml">

    <!--
    <div class="md-layout-item" style=" float:right; margin-top:-10px">
        <md-button type="button" class="md-primary md-raised" v-on:click="reload()" >RELOAD</md-button>
    </div>
-->

<div style="margin-bottom:20px">
    <img style="height: 30px;" src="{{asset("img/silverseas_logo.png")}}"/>
    <h2  style="display: inline-block; vertical-align:bottom; margin:0px; margin-left:30px" v-text="trip.boat_name"></h2>

</div>

    <div v-for="(e,i,pos) in trip.engines" v-cloak  class="engine" v-bind:class="{ opened: drawers['engine_'+i] }">

        <div class="header" v-on:click="openDrawer(i)">
            <i class="material-icons  cursor">arrow_drop_down</i>
            <h3 v-text="e.name + ' ('+e.engine_vib_id+')'"></h3>
            <div class="right_infos" v-if="e.m != null && e.m!=undefined">
                <span>Measured Time: @{{ e.m["DateAndTime"] }} UTC</span>
                <span>Status: @{{ e.m["Status"] }}</span>
                <span v-if="e.m['ChannelSpeed'] !=undefined">RPM: @{{e.m["ChannelSpeed"]  }}</span>
            </div>
            <div class="right_infos" v-else>
                <span>NO DATA IN THE RANGE SELECTED</span>
            </div>

        </div>
        @php
            $limit = 600;
            $red = 500;
            $grad = 50;

        @endphp
        <div class="content" v-if="e.m!=null && e.m!=undefined && e.m['DateAndTime'] !=undefined">

            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-25" style="padding-left: 15px; padding-top:15px; padding-bottom: 50px;">
                    <canvas v-bind:id="'spider-chart-data-'+e.id"></canvas>
                </div>
                <div class="md-layout-item">
                    <div class="md-layout">
                        
                        <div class="md-layout-item">
                            <div class="speedmeter mechanical">
                                <div class="speedmeter-zone">
                                    <img class="meter" src="/img/speedmeter.png"/>
                                    <img class="aiguille" v-bind:style="{ transform: getRotateAiguille(getValue( e.m['MechanicalHealth'], 'valueInPercent'))}" src="{{asset("img/aiguille.png")}}"/>

                                </div>
                                <div class="speedmeter-info">
                                    <span class="speedmeter-value"   v-text="getValue( e.m['MechanicalHealth'], 'valueInPercent')+'%'"></span><br/>
                                    <span class="speedmeter-title" data-tooltip="Overall indication of the mechanical condition of the engine">Mechanichal Health <i class="material-icons help">info</i></span>
                                    <div class="tiret"></div>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="speedmeter termal">
                                <div class="speedmeter-zone">
                                    <img class="meter" src="{{asset("img/speedmeter.png")}}" style="transform: scaleX(1)"/>
                                    <img class="aiguille" v-bind:style="{ transform: getRotateAiguille(getValue( e.m['EngineEfficiency'], 'valueInPercent'))}" src="{{asset("img/aiguille.png")}}"/>
                                </div>
                                <div class="speedmeter-info">
                                    <span class="speedmeter-value"  v-text="getValue( e.m['EngineEfficiency'], 'valueInPercent')+'%'"></span><br/>
                                    <span class="speedmeter-title" data-tooltip=" Overall indication of how efficiently the engine is running (operating condition and setting). Pressure imbalance being unevenly distributed over the cylinders due to combustion gasses">Engine Efficiency <i class="material-icons help">info</i></span>
                                    <div class="tiret"></div>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="tachymeter">
                                
                                <div class="speedmeter-info">
                                    <span class="speedmeter-value"  v-text="e.m['ChannelSpeed'] || '0'"></span><br/>
                                    <span class="speedmeter-title">SPEED (RPM)</span>
                                </div>
                                <div class="tachymeter-diag-zone">
        
                                    <div class="tick" v-if="e.m['ChannelSpeed']" v-bind:style="{ left: (100*e.m['ChannelSpeed']/{{$limit}})+'%' }"></div>
                                    <span class="green-zone" v-bind:style="{ width: (100*{{$red}}/{{$limit}})+ '%' }"></span><span class="red-zone" v-bind:style="{ width: (100*({{$limit-$red}})/{{$limit}})+'%' }"></span>
        
                                </div>
                                <div class="tachymeter-diag-legend">

                                    @php $j =0 @endphp
                                    @for($i=0;$i<$limit;$i+=$grad)
                                    <span class="grad" v-bind:style="{ 'width': {{100*$grad/$limit}} + '%' }">
                                        @if($j%2==0)
                                            <span class="grad-tiret"></span><span class="grad-text">{{$i}}</span>
                                        @else 
                                            <span class="grad-tiret-short"></span>
        
                                        @endif
                                    </span>
                                        @php $j++ @endphp
                                    @endfor
        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md-layout-item block-table md-size-30">
                    <table class="indicators">
                        <tr class="header">
                            <th style="text-align: left">INDICATOR</th>
                            <th>ALARM</th>
                        </tr>
                        <tr>
                            <td data-tooltip="Indicates global balancing of the engine. Excessive vibrations of engine frame, mounts (stiffness, over-aged,…), etc">Mounts % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(0)}" v-text="getValue(e.m['Unbalance'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Dynamic torsion (expressed in degrees) in the crankshaft, measured in each operating cycle">Twist % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(0)}" v-text="getValue(e.m['Acyclism'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Presence of unexpected deformation of the crankshaft, an indicator of mechanical fatigue">Stresses/Pulses % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(0)}" v-text="getValue(e.m['Stresses'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Irregularities of external or auxiliary equipment (in)directly driven by the crankshaft. Status of the pumps, camshafts or other external components (turbocharger, coupling, shaft line,...) not connected to the crankshaft">Cam & Pump Regulation % <i class="material-icons help">info</i> </td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(0)}" v-text="getValue(e.m['CamPump'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Overall indication of the torsional vibration damper activity at current rotational speed">Damper % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(0)}" v-text="getValue(e.m['Damper'],'valueInPercent')"></td>
                        </tr>
                    </table>
                    
                    <img class=" md-layout" align="right" style="
                    height: 70px;
                    margin-top: 20px;" src="{{asset("img/impedance_logo_powered.png")}}" />
                </div>
            </div>

            <div class="md-layout md-gutter">

                <div class="block-table md-layout-item">
                    <table class="indicators_global">
                        <tr class="header">
                            <th style="text-align: left">INDICATOR</th>
                            <th>ALARM</th>
                        </tr>
                        <tr>
                            <td data-tooltip="Power contribution of indicated cylinders deviated from the other cylinders (max cylinder pressures imbalance)">OverPower  % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(getValue(e.m['InjectionOver'],'valueInPercent')/100)}" 
                            v-text="getValue('InjectionOver','valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Uniformity of distribution of cylinder pressures related to dispersion during combustion phase (influence of injection timing)">Injection time % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(getValue(e.m['Injection'],'valueInPercent')/100)}" v-text="getValue(e.m['Injection'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Ignition quality (fuel atomization) due to firing process, indicated fuel injectors (the fuel may not be dispersed well in indicated cylinder(s)), fuel pump(s), etc">Injection condition % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(getValue(e.m['InjectionCondition'],'valueInPercent')/100)}" v-text="getValue(e.m['InjectionCondition'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Dynamic behavior of the (slide) bearings and all moving parts (crankshaft, conrods, pistons, connecting rods,…) per cylinder over multiple revolutions">Moving parts/bearings % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(getValue(e.m['Bearing'],'valueInPercent')/100)}" v-text="getValue(e.m['Bearing'],'valueInPercent')"></td>
                        </tr>
                        <tr>
                            <td data-tooltip="Temporal regularity of compression between cylinders (deviations in compression) linked to (in)correct valve timing, leakage due to a damaged valve, valve seat, liner or piston rings, etc">Compression/valves % <i class="material-icons help">info</i></td>
                            <td v-bind:style="{ 'background-color': colorizeProperty(getValue(e.m['Compression'],'valueInPercent')/100)}" v-text="getValue(e.m['Compression'],'valueInPercent')"></td>
                        </tr>
                    </table>
                </div>
                <div class="block-table md-layout-item">
                    <table class="indicators_cylinder">
                        <tr class="header">
                            <th v-for="(e,i) in e.m['Engine']['FiringOrder']" v-text="e"></th>
                        </tr>
                        <tr v-if="e.m['InjectionOver'] && e.m['InjectionOver']['cylinderValues']">
                            <td v-for="(v,i) in e.m['InjectionOver']['cylinderValues']" v-bind:style="{ 'background-color': colorizeProperty(v/100)}" v-text="(v/100).toFixed(2)"></td>
                        </tr>
                        <tr v-else> <td v-for="index in e.m['Engine']['FiringOrder'].length" :key="index" style="background-color: #666666">0.00</td> </tr>
                        <tr v-if="e.m['Injection'] && e.m['Injection']['cylinderValues']" >
                            <td v-for="(v,i) in e.m['Injection']['cylinderValues']" v-bind:style="{ 'background-color': colorizeProperty(v/100)}" v-text="(v/100).toFixed(2)"></td>
                        </tr>
                        <tr v-else> <td v-for="index in e.m['Engine']['FiringOrder'].length" :key="index" style="background-color: #666666">0.00</td> </tr>
                        <tr v-if="e.m['InjectionCondition'] && e.m['InjectionCondition']['cylinderValues']">
                            <td v-for="(v,i) in e.m['InjectionCondition']['cylinderValues']" v-bind:style="{ 'background-color': colorizeProperty(v/100)}" v-text="(v/100).toFixed(2)"></td>
                        </tr>
                        <tr v-else> <td v-for="index in e.m['Engine']['FiringOrder'].length" :key="index" style="background-color: #666666">0.00</td> </tr>
                        <tr v-if="e.m['Bearing'] && e.m['Bearing']['cylinderValues']">
                            <td  v-for="(v,i) in e.m['Bearing']['cylinderValues']" v-bind:style="{ 'background-color': colorizeProperty(v/100)}" v-text="(v/100).toFixed(2)"></td>
                        </tr>
                        <tr v-else> <td v-for="index in e.m['Engine']['FiringOrder'].length" :key="index" style="background-color: #666666">0.00</td> </tr>
                        <tr v-if="e.m['Compression'] &&  e.m['Compression']['cylinderValues']">
                            <td v-for="(v,i) in e.m['Compression']['cylinderValues']" v-bind:style="{ 'background-color': colorizeProperty(v/100)}" v-text="(v/100).toFixed(2)"></td>
                        </tr>
                        <tr v-else> <td v-for="index in e.m['Engine']['FiringOrder'].length" :key="index" style="background-color: #666666">0.00</td> </tr>

                    </table>
                </div>
            </div>

            <div style="margin-bottom: 10px; margin-top:-10px">

                <canvas v-bind:id="'chart-data-'+e.id" class="graph">
                </canvas>
            </div>
        </div>
    </div>
    <h3 v-if="trip.engines.length==0">No engines data yet</h3>

    <div class="filter" style="margin-top:50px; font-family: Nunito,sans-serif;">

        <div class="md-layout">

            <div class="md-layout-item md-size-20">
                <label>Granularity</label>

                <div class="md-layout">
                    <div class="md-layout-item  md-size-70">
                        <md-field>
                            <md-input v-model="granularity" step="1" type="number"></md-input>
                        </md-field>
                    </div>
                </div>
            </div>

            <div class="md-layout-item md-size-35">
                <label>From</label>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-size-50">
                        <md-datepicker v-model="fromDate" md-immediately></md-datepicker>
                    </div>
                    <div class="md-layout-item md-size-30">
                        <md-field>
                            <md-input v-model="fromTime" type="time"></md-input>
                        </md-field>
                    </div>
                </div>
            </div>
            <div class="md-layout-item md-size-35">
                <label>To</label>
                <div class="md-layout md-gutter">

                    <div class="md-layout-item md-size-50">

                        <md-datepicker v-model="toDate" md-immediately></md-datepicker>
                    </div>
                    <div class="md-layout-item md-size-30">
                        <md-field>
                            <md-input v-model="toTime" type="time"></md-input>
                        </md-field>
                    </div>
                </div>
            </div>
            <div class="md-layout-item md-size-10" style="margin-top: 20px">
                <md-button type="button" class="md-primary md-raised" v-on:click="filterDates" >Filter</md-button>
            </div>
        </div>


    </div>

    @isset($ts)
    <table>
        @foreach($ts as $t)
        <tr>
            <td> {{$loop->index}}</td>
            <td> {{$t}}</td>
        </tr>
        @endforeach
    </table>
    @endisset
</div>

@endsection

@push("endscripts")

    @php
        $drawers = [];
        foreach($trip["engines"] as $key=>$engine)
        {
            $drawers["engine_".$key] = ($key == 0);
        }

        $dataSelected = [];

        foreach($trip->engines as $k=>$e)
        {
            if(isset($data[$e->id]) && count($data[$e->id])>0)
                $trip->engines[$k]["m"] = $data[$e->id][count($data[$e->id])-1]->json;
            else {
                $trip->engines[$k]["m"] = null;
            }
        }


    @endphp

    <script src="{{asset("libs/js/moment.min.js")}}"></script>
    <script src="{{asset("libs/js/chart.min.js")}}"></script>

    <script>
        var datasetsLoc =[];
        var labelsLoc =[];
        var engines = new Vue({
            el:"#engines",
            data:{
                trip:@json($trip),
                data:@json($data),
                drawers:@json($drawers),
                granularity:{{$granularity}},
                fromDate:"{{substr($from_date,0,10)}}",
                fromTime:"{{substr($from_date,11,5)}}",
                toDate:"{{substr($to_date,0,10)}}",
                toTime:"{{substr($to_date,11,5)}}",
                labels:labelsLoc,
                datasets : datasetsLoc,
                radarCharts:[],
                charts:[]
            },

            mounted(){
                this.createCharts();
                this.createSpiderCharts();
            },

            methods:{

                createCharts:function(){
                    for(var i=0;i<this.trip.engines.length;i++)
                    {

                        var engine = this.trip.engines[i];
                        var mechanicalDatas = [];
                        var termalDatas = [];
                        var rpmDatas = [];
                        var dateLoc = [];
                        for(var j=0;j<this.data[engine.id].length;j++){
                            var d = this.data[engine.id][j];
                            var date = d["datetime"].replace(" ","T")+"Z";
                            //console.log(date);
                            dateLoc.push(date);
                            if(d["json"]["Status"]<0){
                                
                                rpmDatas.push({
                                    t:date,
                                    y:parseFloat(0)
                                });
                                
                                mechanicalDatas.push({
                                    t:date,
                                    y:parseFloat(0)
                                });
                                termalDatas.push({
                                    t:date,
                                    y:parseFloat(0)
                                });
                            }else{

                                rpmDatas.push({
                                    t:d["datetime"],
                                    y:parseFloat(d["json"]["ChannelSpeed"])
                                });
                                mechanicalDatas.push({
                                    t:d["datetime"],
                                    y:parseFloat(d["json"]["MechanicalHealth"]["valueInPercent"])
                                });
                                termalDatas.push({
                                    t:d["datetime"],
                                    y:parseFloat(d["json"]["EngineEfficiency"]["valueInPercent"])
                                });
                            }
                        }
                        var slug = 'chart-data-'+engine.id;
                        var vm = this;

                        //TODO CHANGE
                        var isDayRange = true;

                        var gridColor = "#FFFFFF22"
                        //var gridColor = "#092345";

                        this.createChart(slug, {
                            type: 'line',
                            data: {
                                labels:dateLoc,
                                datasets:[
                                    {
                                        label:"RPM",
                                        data:rpmDatas,
                                        yAxisID: 'B',
                                        fill: false,
                                        borderColor: "rgb(123,203,139)",
                                        pointBackgroundColor: 'rgb(123,203,139)',
                                        backgroundColor: 'rgba(123,203,139,0.49)',
                                        pointRadius: 3,
                                        showLine: false
                                    },
                                    {
                                        label:"Mechanical Health",
                                        data:mechanicalDatas,
                                        yAxisID: 'A',
                                        borderColor: "#00a4dc",
                                        pointBackgroundColor: '#00a4dc',
                                        backgroundColor: 'rgba(0,164,220,0.5)',
                                        pointRadius: 3,
                                        fill: false,
                                        showLine: false
                                    },
                                    {
                                        label:"Engine Efficiency",
                                        data:termalDatas,
                                        yAxisID: 'A',
                                        borderColor: "#d339e0",
                                        pointBackgroundColor: '#d339e0',
                                        backgroundColor: 'rgba(211,57,224,0.5)',
                                        pointRadius: 3,
                                        fill: false,
                                        showLine: false
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                lineTension: 1,
                                legend:{
                                    position:"bottom",
                                    labels: {
                                        fontColor: '#FFFFFF',
                                        usePointStyle: true
                                    }
                                },
                                scales: {
                                    xAxes:[{
                                        gridLines: {
                                            color: gridColor
                                        },
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'Time',
                                            fontColor: '#FFFFFF',
                                        },
                                        ticks:{

                                            fontColor: "white",
                                            autoSkip: true,
                                            maxTicksLimit: 10
                                        },
                                        type: 'time',
                                        distribution: 'linear',
                                        time: {
                                            minUnit:'minute',
                                            displayFormats: {
                                                millisecond: 'HH:mm:ss.SSS',
                                                second: 'HH:mm:ss',
                                                minute: 'HH:mm',
                                                hour: 'MMM D, HH:mm',
                                                day: 'MMM D HH:mm',
                                                month: 'YYYY MMM',
                                                year: 'YYYY'
                                            },
                                        }
                                    }],
                                    yAxes: [{
                                        id: 'A',
                                        gridLines: {
                                            color: gridColor
                                        },
                                        type: 'linear',
                                        position: 'left',
                                        ticks: {
                                            fontColor: "white",
                                            beginAtZero: true
                                        },
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'Health (%)',
                                            fontColor: '#FFFFFF',
                                        },
                                    },{
                                        id: 'B',
                                        type: 'linear',
                                        position: 'right',
                                        gridLines: {
                                            display:false,
                                            color: "#092345"
                                        },
                                        ticks: {
                                            fontColor: "white",
                                            beginAtZero: true
                                        },
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'RPM',
                                            fontColor: '#FFFFFF',
                                        },

                                    }]
                                }
                            }

                        });
                    }
                },

                getValue:function(e, key){
                    if(e == undefined) return 0;
                    if(e[key] == undefined) return 0;
                    return e[key];
                },

                getDataEngines:function(){
                    axios.get("/data_engines/"+this.trip.id+"?toDate="+this.toDate+" "+this.toTime+"&fromDate="+this.fromDate+" "+this.fromTime).then(res=>{
                        
                        this.data= res.data.data;
                        this.trip = res.data.trip;
                        
                        for(var i=0;i<this.trip.engines.length;i++) {

                            var engine = this.trip.engines[i];
                            var mechanicalDatas = [];
                            var termalDatas = [];
                            var rpmDatas = [];
                            var dateLoc = [];

                            for(var j=0;j<this.data[engine.id].length;j++){
                                var d = this.data[engine.id][j];
                                dateLoc.push(d["datetime"]);
                                if(d["json"]["Status"]<0) continue;
                                rpmDatas.push(parseFloat(d["json"]["ChannelSpeed"]));
                                mechanicalDatas.push(parseFloat(d["json"]["MechanicalHealth"]["value"]));
                                termalDatas.push(parseFloat(d["json"]["EngineEfficiency"]["value"]));
                            }
                            this.trip.engines[i].m=this.data[engine.id][this.data[engine.id].length-1]["json"];
                            if(this.charts[i]!=undefined)
                            {
                                
                                this.charts[i].data.datasets[0].data = rpmDatas ;
                                this.charts[i].data.datasets[1].data = mechanicalDatas ;
                                this.charts[i].data.datasets[2].data = termalDatas ;
                                this.charts[i].data.labels = dateLoc ;
                                this.charts[i].update();
                            }


                        }

                    });
                },
                openDrawer:function (i) {
                    //console.log("drawer i="+this.drawers["engine_"+i]);
                    this.drawers["engine_"+i] = !this.drawers["engine_"+i];
                },
                colorizeProperty : function(v){
                    if(v<0.5)
                        return "rgb("+v*255+","+ (+v*135+120)+","+(-120*v +120)+")";
                    else
                        return "rgb("+(255-v*55)+","+ (255-v*155)+",0)";

                    if(v>0.80) return "rgb(200,100,0)";
                    if(v>0.20) return "rgb(255,255,0)";
                    return "rgb(0, 120, 120)";
                },
                createChart(chartId, chartData) {
                    //console.log("chartId = "+chartId);
                    if(chartData.data == null) return;
                    const ctx = document.getElementById(chartId);
                    if(ctx == null || ctx == undefined) return;
                    const myChart = new Chart(ctx, {
                        type: chartData.type,
                        data: chartData.data,
                        options: chartData.options,
                    });
                    this.charts.push(myChart);

                    var vm = this;
                    document.getElementById(chartId).onclick = function(evt){
                        var activePoints = myChart.getElementsAtEvent(evt);
                        //console.log(activePoints);
                        var firstPoint = activePoints[0];
                        //console.log(firstPoint);
                        if(firstPoint !== undefined) {
                            var label = myChart.data.labels[firstPoint._index];
                            //console.log(label);
                            for(var i=0;i<vm.trip.engines.length;i++) {
                                var engine = vm.trip.engines[i];
                                if(vm.data[engine.id] != null)
                                {
                                    //console.log(vm.data[engine.id]);
                                    for (var j = 0; j < vm.data[engine.id].length; j++) {
                                        var d = vm.data[engine.id][j];
                                        var date = d["datetime"].replace(" ","T")+"Z";
                                        if(date==label)
                                        {
                                            vm.trip.engines[i].m =d["json"]
                                        }
                                    }
                                }
                                
                                if(vm.radarCharts[i]!=null) vm.radarCharts[i].update();
                            }
                        }
                        engines.createSpiderCharts();
                    };
                    
                    //console.log("chartId = "+chartId+"  chartCreated");
                },
                getRotateAiguille(value)
                {
                    if(value==undefined)                     
                    return "translate(-50%,-50%) rotateZ(0deg)";

                    return "translate(-50%,-50%) rotateZ("+((value-50)*2.4)+"deg)";
                },
                filterDates:function () {
                    window.location.href = "/engines"+"?toDate="+this.toDate+" "+this.toTime+"&fromDate="+this.fromDate+" "+this.fromTime+"&granularity="+this.granularity
                },

                reload:function(){
                    document.location.reload(true);
                },

                revertArray:function(arr)
                {
                    newArr = [];
                    for(var i=0;i<arr.length;i++)
                    {
                        newArr[i] = 100-arr[i];
                    }
                    return newArr;
                },
                createSpiderCharts() {
                    for(var i=0;i<this.radarCharts.length;i++)
                    {
                        this.radarCharts[i].clear();
                        this.radarCharts[i].destroy();
                    }
                    
                    this.radarCharts = [];
                    
                    for(var i=0;i<this.trip.engines.length;i++)
                    {
                        var e = this.trip.engines[i];
                        if(e.m ==undefined) return;

                        var cylinderValues = [];
                        var injectionValues = [];


                        if(e.m["Status"]<0) {
                            cylinderValues = new Array(this.trip.engines[i].m['Engine']['FiringOrder'].length).fill(0);
                            injectionValues = cylinderValues;
                        }
                        else{

                            if(this.trip.engines[i].m["InjectionOver"] == undefined || this.trip.engines[i].m["InjectionOver"]["cylinderValues"] == undefined)
                                cylinderValues = new Array(this.trip.engines[i].m['Engine']['FiringOrder'].length).fill(0);
                            else
                                cylinderValues = this.trip.engines[i].m["InjectionOver"]["cylinderValues"];

                            if(this.trip.engines[i].m["Injection"] == undefined  || this.trip.engines[i].m["Injection"]["cylinderValues"] == undefined)
                                injectionValues = new Array(this.trip.engines[i].m['Engine']['FiringOrder'].length).fill(0);
                            else
                                injectionValues = this.trip.engines[i].m["Injection"]["cylinderValues"];

                        }

                        console.log(cylinderValues);
                        var radarChat = new Chart(document.getElementById("spider-chart-data-"+this.trip.engines[i].id), {
                            type: 'radar',
                            data: {
                                labels:e.m["Engine"]["FiringOrder"],
                                datasets: [{
                                    data: this.revertArray(cylinderValues),
                                    label:"Power Contribution",
                                    borderColor: "#223f80",
                                    fillColor: "#223f80",
                                    pointBackgroundColor: '#223f80',
                                    backgroundColor: 'rgba(34,63,128,0.51)',
                                    pointRadius: 3,
                                    fill:true
                                },{
                                    data: this.revertArray(injectionValues),
                                    label:"Injection",
                                    fillColor: "#ff7a27",
                                    borderColor: "#ff7a27",
                                    pointBackgroundColor: '#ff7a27',
                                    backgroundColor: 'rgba(255,122,39,0.49)',
                                    pointRadius: 3,
                                    fill:true
                                }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: 0
                                },
                                legend:{
                                    position:"bottom",
                                    align: "start",
                                    labels: {
                                        fontColor: '#FFFFFF'
                                    },
                                },
                                tooltips: {
                                    enabled: true,
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            console.log("test");
                                            return data.datasets[tooltipItem.datasetIndex].label + ' : ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                        }
                                    }
                                },
                                scale: {
                                    angleLines: {
                                        display: true
                                    },
                                    gridLines: {
                                        display: false
                                    },
                                    pointLabels:{
                                        fontColor: '#FFFFFF',
                                        fontSize:14
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 100,
                                        display:false
                                    },
                                }
                            }
                        });

                        this.radarCharts.push(radarChat);
                    }
                }

            },

        })


        function getDatasEngines(){
            document.location.reload(true);
        }
        setInterval(getDatasEngines,300000);
    </script>
@endpush

