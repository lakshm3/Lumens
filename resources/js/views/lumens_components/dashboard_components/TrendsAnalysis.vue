
<template>
  <generic-component
    :editable="editable"
    :engine="engine"
    :extras="conf"
    @extras-changed="onExtrasChanged"
    v-on="$listeners"
  >
  </generic-component>
</template>
<script>
import GenericComponent from "./GenericComponent.vue";
export default {
  components: { GenericComponent },
  props: ["extras", "editable", "engine"],
  data() {
    return {
      conf: {
        sections: [
          {
            align: 0,
            items: [
              {
                type: "title",
                title: this.$t("components.trends.name"),
                style: {
                  size: 0,
                },
              },
              {
                type: "chart",
                chart: {
                  lateralIndicators: [],
                  graphIndicators: [],
                  show_rpm_graph: true,
                  comparative_mode: false,
                  title: "",
                },
              },
            ],
          },
        ],
      },
    };
  },

  mounted() {
    if (this.extras.sections == undefined) {
      //migration v1 trends analysis
      if (this.extras.lateralIndicators != undefined)
        this.conf.sections[0].items[1].chart.lateralIndicators =
          this.extras.lateralIndicators;
      if (this.extras.graphIndicators != undefined)
        this.conf.sections[0].items[1].chart.graphIndicators =
          this.extras.graphIndicators;
      if (this.extras.comparative_mode != undefined)
        this.conf.sections[0].items[1].chart.comparative_mode =
          this.extras.comparative_mode;
      if (this.extras.name != undefined)
        this.conf.sections[0].items[1].chart.title = this.extras.name;
    } else {
      this.conf = this.extras;
    }
  },

  methods: {
    onExtrasChanged(newExtras) {
      this.$emit("extras-changed", newExtras);
    },
  },
};
</script>