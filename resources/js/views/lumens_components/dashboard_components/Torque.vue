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
                title: "Torque",
                style: {
                  size: 1,
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph",
                  unit: "°",
                  showLabel: true,
                  indicator: "StaticTorsion",
                  displayedValue: "value",
                  nbDecimal:2,
                  customColor:true,
                  color:"#7eb338",
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph",
                  unit: "kNm",
                  factor:0.001,
                  customColor:true,
                  color:"#7eb338",
                  max:1000000,
                  showLabel: true,
                  nbDecimal:2,
                  indicator: "StaticTorque",
                  displayedValue: "value",
                },
              },
              {
                type: "meter",
                meter: {
                  meterType: "speedometer-graph",
                  unit: "kW",
                  customColor:true,
                  color:"#7eb338",
                  factor:0.001,
                  nbDecimal:2,
                  max:1000000,
                  showLabel: true,
                  indicator: "StaticPower",
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
