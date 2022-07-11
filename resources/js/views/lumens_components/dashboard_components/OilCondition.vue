<template>
     <dashboard-component class="card" style="min-width:150px;" :editable="editable"  v-on="$listeners">  
            <template v-slot:content>
                <div class="card-title">Oil Condition</div>
                <div style="vertical-align:top; text-align:center">
                <quartermeter label="TDN" style="width:30%" value="6593" max="7000" min="0" subdivs="5" :subdivs_danger="[0,1]" ></quartermeter>
                <quartermeter label="°C"  style="width:30%" value="200" max="700" min="0" subdivs="5" :subdivs_danger="[4]" ></quartermeter>
                <quartermeter label="Rate of ch."  style="width:30%"  value="0" max="700" min="0" subdivs="5" :subdivs_danger="[0,4]"   :subdivs_warn="[1,3]" ></quartermeter>
                </div>
            </template>
     </dashboard-component>

</template>

<script>
export default {
    props:["editable","engine"]
}
</script>