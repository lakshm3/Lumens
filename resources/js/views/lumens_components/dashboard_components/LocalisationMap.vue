

<template>
    <dashboard-component class="card" :editable="editable" v-on="$listeners">
        <template v-slot:content>
            <div class="card-title">Localisation Map</div>
             <div v-bind:id="'map-'+_uid" class="map"></div>

        </template> 
    </dashboard-component>
    

</template>

<style lang="scss" scoped>
.map {
    width: 100%;
    min-height: 100px;
    height: calc( 100% - 50px );
    position: relative;
    outline: none;
}
</style>
<script>
export default {
    props:["editable"],
    
    mounted(){
        this.initMap();
        var vm = this;
        setTimeout(function(){ vm.map.invalidateSize()}, 400);

    },
    data(){
        return {
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
        }
    },
    methods:{
        initMap(){
            console.log('map-'+this._uid);
            this.map =  L.map('map-'+this._uid).setView([51.505, -0.09], 13);
            var mapBoxToken = "pk.eyJ1IjoiamVyb21lZ2F1emlucyIsImEiOiJja2IyMzRhbWMwNmswMnBwaHczejlpdmNqIn0.OW0l1wH1xxximGeruU3_5Q";
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: mapBoxToken
            }).addTo(this.map);
        },
    }
}
</script>