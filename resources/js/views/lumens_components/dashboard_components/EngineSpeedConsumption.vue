<template>
    <dashboard-component class="card" minWidth="120px" :editable="editable" v-on="$listeners" >
        <template v-slot:content>
            <div class="card-title">Engine</div>
            <div style="text-align:center;margin-bottom:12px">
                <speedometer color="#7eb338" :max="6000" :value="getMeasure('ChannelSpeed')" minWidth="150px" type="0" unit="rpm" title="Speed"></speedometer>
                <speedometer max="2000" color="#7eb338" :value="200" minWidth="150px" type="0" unit="kg/h" title="Fuel Consumption"></speedometer>
            </div>
        </template>
    </dashboard-component>
</template>
<script>
export default {
    props:["engine","editable"],
    
    methods:{
        
        getMeasure:function(key, key2){
            if(this.engine !=undefined && this.engine.measure != undefined) 
                return parseInt(this.engine.measure[key]);
            return 0;
        },
    }

}
</script>