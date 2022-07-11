
<template>
    <resizable-card class="quatermeter" keepRatio="true" dim="px">
        <template v-slot:content>
            <div class="quatermeter-graph">
            <svg class="progress-ring"  :width="getMinWidth()" :height="getMinWidth()">
                <circle class="progress-ring__circle"
                    stroke="white"
                    stroke-width="20"
                    fill="transparent"
                    :r="getRadius()" :cx="0" :cy="0"
                    :style="{'stroke-dashoffset': getFinalValue(getRadius()), 'strokeDasharray': getRadius() * 2 * Math.PI }"/>

                <circle class="progress-ring__circle"
                    stroke="#595959"
                    stroke-width="16"
                    fill="transparent"
                    :r="getRadius()-4"  :cx="0" :cy="0"
                    :style="{'stroke-dashoffset': getFinalValue(getRadius()-4), 'strokeDasharray': (getRadius()-4) * 2 * Math.PI }"/>

                <circle 
                    v-for="i in  5" :key="i"
                    class="progress-ring__circle"
                    :stroke="getSubdivColor(5-i)"
                    stroke-width="16"
                    fill="transparent"
                    :r="getRadius()-4" :cx="0" :cy="0"
                    :style="{'stroke-dashoffset': getPathCircle(6-i),
                            'strokeDasharray': (getRadius()-4) * 2 * Math.PI}"/>
                
                </svg>
                    
                <div class='needle' :style="{'transform': 'rotate('+getAiguilleRotate()+'deg)'}"></div>
        </div>
        

        <div class="quatermeter-measure" v-text="label+': '+value"></div>
        </template>
        </resizable-card>

</template>

<script>
export default {
    data(){
        return {
            minWidth:undefined
        };
    },

    methods:{
        getAiguilleRotate:function(){
            return ((this.value-this.min)/(this.max-this.min) *90);
        },
        getPathCircle:function(i){
            return (this.getRadius()-4) * 2*Math.PI * (1- 0.25*(i)/(this.subdivs));
        },
        getSubdivColor:function(index){
            if(this.subdivs_danger == undefined) return "rgb(0,200,0)";
            for(var i=0;i<this.subdivs_danger.length;i++){
                if(index == this.subdivs_danger[i])
                    return "rgb(255,0,0)";
            }
            if(this.subdivs_warn == undefined) return "rgb(0,200,0)";
            for(var i=0;i<this.subdivs_warn.length;i++){
                if(index == this.subdivs_warn[i])
                    return "rgb(225,180,0)";
            }
            return "rgb(0,200,0)";
        },
        getFinalValue:function(radius){
            return radius * 2 * Math.PI *0.75;
        },
        getValue:function(){
            return (this.getRadius()-4) * 2 * Math.PI *(1-1/(4*this.subdivs));
        },

        getRadius:function(){
            if(this.radius == undefined) return this.getMinWidth()-10;
            else return this.radius;
        },
        getMinWidth:function(){
            if(this.minWidth == undefined) return 100;
            else return this.minWidth
        },
        
    },
    props: ['label','value','subdivs','subdivs_danger','subdivs_warn','max','min']
}
</script>