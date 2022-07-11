

@extends("app")

@section("content")

    <div id="create_trip">

        <form novalidate class="md-layout " @submit.prevent="validateTrip">
            <md-card class="md-layout-item ">
                <md-card-header>
                    <div class="md-title">Settings of the monitoring</div>
                </md-card-header>

                <md-card-content>
                    <div class="md-layout md-gutter">

                        <div class="md-layout-item">
                            <md-field>
                                <label>Name of the trip</label>
                                <md-input v-model="trip.name"></md-input>
                            </md-field>
                        <md-field>
                            <label>Name of the boat</label>
                            <md-input v-model="trip.boat_name"></md-input>
                        </md-field>
                            <br/>

                            <!--
                            <div v-for="(v,id) in trip.engines" class="md-layout-item">

                                <div class="md-layout-item">
                                    <label style="vertical-align: bottom; margin-top: 20px">Vibox @{{id+1  }}</label>
                                </div>
                                <div class="md-layout md-gutter">
                                    <div class="md-layout-item">
                                        <md-field>
                                            <label>Name of the engine associated</label>
                                            <md-input v-model="v.name"></md-input>
                                        </md-field>
                                    </div>
                                    <div class="md-layout-item">
                                        <md-field>
                                            <label>MAC ID of VIBOX</label>
                                            <md-input v-model="v.mac_id"></md-input>
                                        </md-field>
                                    </div>
                                    <div class="md-layout-item">
                                        <md-button type="button" class="md-accent md-raised" v-on:click="deleteVibox(id)" :disabled="sending" >Delete this VIBOX</md-button>
                                    </div>
                                </div>


                            </div>
                            <md-button type="button" class="md-primary md-raised" v-on:click="addVibox" :disabled="sending" >Add A Vibox</md-button>
                        -->

                        </div>
                    </div>


                </md-card-content>

                <md-progress-bar md-mode="indeterminate" v-if="sending"></md-progress-bar>


                <md-card-actions>
                    <md-button type="submit" class="md-primary" :disabled="sending" >Save the settings</md-button>
                </md-card-actions>

            </md-card>

        </form>

    </div>


    <style lang="scss" scoped>
        .md-progress-bar {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
        }
    </style>
@endsection

@push("endscripts")
    <script>

        var createTrip = new Vue({
            el:"#create_trip",
            data:{
                sending:false,
                trip:@json($trip)
            },
            
            methods:{
                validateTrip:function () {
                    this.sending = true;

                    axios.put("{{route("trips.store")}}/"+this.trip.id,this.trip).then(res =>{
                        console.log(res.data);
                        this.sending = false;
                        window.location.href="/"
                    }).catch(res =>{
                        this.sending = false;
                    })
                },
                addVibox:function(){
                    this.trip.engines.push({
                        name:"Engine "+(this.trip.engines.length+1),
                        mac_id:"00:30:90:80:00:0C"
                    });
                },
                deleteVibox:function(id){
                    console.log("delte vibox id="+id);
                    this.trip.engines.splice(id,1)
                },


            }
        })
    </script>
@endpush
