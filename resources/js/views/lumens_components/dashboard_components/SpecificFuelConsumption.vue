<template>
    
    <dashboard-component class="card" id="test"  width="calc(100% - 20px)" minWidth="188px" 
    :editable="editable" v-on="$listeners" >
        <template v-slot:content>
            <div class="card-title">Specific Fuel Consumption</div>
            <div style="text-align:center;" class="main_meters">
                <speedometer value="58" minWidth="150px" unit="g/kWh" title="Specific Fuel Consumption"></speedometer>
            </div>
        </template>
    </dashboard-component>
</template>
<script>
export default {
    props:["engine","editable"],
    methods:{
        
        getMeasure:function(key, key2){
            if(this.engine !=undefined && this.engine.measure != undefined && this.engine.measure[key] != undefined) 
                return parseInt(this.engine.measure[key][key2]);
            return 0;
        },
    }
}
</script>