<template>
  <interface-view :ui="ui"></interface-view>
</template>
<script>
import InterfaceView from "./lumens_components/InterfaceView.vue";
export default {
  components: { InterfaceView },
  data(){
    return {
      ui:null
    }
  },
  mounted(){
    this.getUI()
  },
  methods: {
    getUI() {
      axios.get("/api/interfaces").then((res) => {
        this.ui = res.data[0];
        console.log("ui retrieved");
        console.log(this.ui);
      });
    },
  },
};
</script>