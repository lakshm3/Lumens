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
                title: "Shaft Diagnostic",
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
                  indicator: "MStressStability",
                  displayedValue: "valueInPercent",
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "%",
                  showLabel: true,
                  indicator: "MBearing",
                  displayedValue: "valueInPercent",
                },
              },

              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph-quarter",
                  unit: "°",
                  max:1,
                  invertedColor: true,
                  nbDecimal: 2,
                  showLabel: true,
                  indicator: "BS_TwistAngle",
                  displayedValue: "value",
                },
              },
            ],
          },
        ],
      },
    };
  },

  mixins: [DashboardComponentUtility],
  mounted() {
    if (this.extras.sections != undefined) {
      this.conf = this.extras;
    }
  },

  methods: {
    onExtrasChanged(a, b, c) {
      this.$emit("extras-changed", a, b, c);
    },
  },
};
</script>
