<template>
  <generic-component
    :engine="engine"
    :editable="editable"
    v-on="$listeners"
    :extras="conf"
    @extras-changed="onExtrasChanged"
  >
  </generic-component>
</template>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";
import GenericComponent from "./GenericComponent.vue";

export default {
  components: { GenericComponent },
  props: ["engine", "editable", "extras"],
  data() {
    return {
      conf: {
        sections: [
          {
            align: 0,
            items: [
              {
                type: "title",
                title: this.$t("components.compressor.name"),
                style: {
                  size: 1,
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "%",
                  showLabel: true,
                  indicator: "CCompression",
                  displayedValue: "valueInPercent",
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "%",
                  showLabel: true,
                  indicator: "CPalier",
                  displayedValue: "valueInPercent",
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "°",
                  showLabel: true,
                  indicator: "TwistAngle",
                  displayedValue: "value",
                  coloredValue: "valueInPercent",
                  nbDecimal:2
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "",
                  showLabel: true,
                  indicator: "CCompression_cyl",
                  displayedValue: "value",
                  coloredValue: "valueInPercent",
                },
              }
            ],
          },
        ],
      },
    };
  },

  mixins: [DashboardComponentUtility],
  mounted() {
    console.log("extras =>" + this.extras);
    console.log(this.extras);
    if (this.extras.sections != undefined) {
      this.conf = this.extras;
    }
  },

  methods: {
    onExtrasChanged(a, b, c) {
      console.log("extras changed !");
      this.$emit("extras-changed", a, b, c);
    },
  },
};
</script>
