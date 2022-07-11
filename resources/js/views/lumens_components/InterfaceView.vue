<template>
  <div style="text-align: left">
    <p v-if="error.length > 0" class="error" v-text="error"></p>
    <loader v-if="loading"></loader>

    <div v-else>
      <template v-for="(zone, i) in config.zones">
        <dashboard-component
          class="engine dropable_zone"
          :key="config.name + '-' + i"
          v-if="getHardwareForZone(zone) || zone.type == 'Other'"
          :ref="'zone-' + i"
        >
          <template v-slot:content>
            <h2 style="margin: 0px 10px" v-text="getZoneName(zone)"></h2>

            <component
              v-for="(component, j) in zone.components"
              :key="config.name + '-' + j"
              :ref="'component-' + i + '-' + j"
              :indicators="config.indicators"
              :extras="component.extras"
              :engine="getHardwareForZone(zone)"
              @measureSelected="onMeasureSelected"
              :hardwares="hardwares"
              :is="getComponentByTitle(component.name)"
            ></component>
          </template>
        </dashboard-component>
      </template>

      <div v-if="cantDesignThatEquipment()">
        <HardwaresSummary :hardwares="hardwares"></HardwaresSummary>
        <div style="padding: 10px; display: block; clear: both">
          <p>
            The current selected interface cannot design that equipment. Please
            make an interface that can host this equipment by clicking here
          </p>
          <button
            @click="$router.push({ name: 'interfaces-create' })"
            v-if="adminConnected"
          >
            Create an interface
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import components from "./dashboard_components";

import DashboardComponentUtility from "./DashboardComponentUtility.js";
import DashboardComponent from "./DashboardComponent.vue";
export default {
  components: { ...components, DashboardComponent },
  mixins: [DashboardComponentUtility],
  props: ["ui"],
  data: function () {
    return {
      hardwares: [],
      loading: true,
      equipment: {
        _id: this.$route.params.equipment_id,
      },
      //hardwareZones: [],
      config: {},
      error: "",
    };
  },
  mounted() {
    if (this.ui != null) {
      this.config = JSON.parse(this.ui.config);
    }
    this.getHardwares();
  },
  methods: {
    cantDesignThatEquipment() {
      if (this.config == null || this.config.zones == null) return true;
      var hasOneMin = false;

      for (var i = 0; i < this.config.zones.length; i++) {
        var h = this.getHardwareForZone(this.config.zones[i]);
        console.log("config " + i + ": ", this.config.zones[i], h);
        if (h != null) hasOneMin = true;
      }
      return !hasOneMin;
    },
    getHardwareForZone(zone) {
      return this.hardwares.find((h) => h.id == parseInt(zone.engine_number));
    },

    getZoneName(zone) {
      var hardware = this.getHardwareForZone(zone);
      if (hardware == null) return zone.name;
      else if (hardware.name != null) return hardware.name;
      return zone.name;
    },
    onMeasureSelected: function (idHardware, measure, ts) {
      console.log(
        "OnMeasureSelected => idHardware:" +
          idHardware +
          ", measure:" +
          measure +
          ", ts:" +
          ts
      );
      for (var i = 0; i < this.hardwares.length; i++) {
        if (
          this.hardwares[i]._id == idHardware ||
          this.hardwares[i].id == idHardware
        ) {
          console.log("Hardware Found ! ts=" + ts);

          this.$set(this.hardwares[i], "measure", measure);
          if (ts) this.$set(this.hardwares[i], "date", this.tsToDate(ts));
        }
      }
    },

    applyCurrentInterface: function () {
      console.log("UI");
      console.log(this.ui);

      this.config = JSON.parse(this.ui.config);
      this.indicators =
        this.config.indicators == undefined ? [] : this.config.indicators;

      this.$nextTick(function () {
        var vm = this;
        for (var i = 0; i < this.config.zones.length; i++) {
          var zone = this.config.zones[i];
          if (this.$refs["zone-" + i] == undefined) continue;

          for (var j = 0; j < zone.components.length; j++) {
            var ref = this.$refs["component-" + i + "-" + j];
            if (ref == undefined) continue;

            var el = ref[0].$el;
            el.style.width = zone.components[j].width;
            el.style.minWidth = zone.components[j].minWidth;
            el.style.maxWidth = zone.components[j].maxWidth;
            el.style.minHeight = zone.components[j].minHeight;
            el.style.maxHeight = zone.components[j].maxHeight;
            el.style.height = zone.components[j].height;
            var subElts = el.querySelectorAll(".resizable");
            if (subElts != null) {
              for (var k = 0; k < subElts.length; k++) {
                if (zone.components[j].subcomponents[k] != undefined) {
                  subElts[k].style.width =
                    zone.components[j].subcomponents[k].width;
                  subElts[k].style.minWidth =
                    zone.components[j].subcomponents[k].minWidth;
                  subElts[k].style.maxWidth =
                    zone.components[j].subcomponents[k].maxWidth;
                  subElts[k].style.minHeight =
                    zone.components[j].subcomponents[k].minHeight;
                  subElts[k].style.maxHeight =
                    zone.components[j].subcomponents[k].maxHeight;
                  subElts[k].style.height =
                    zone.components[j].subcomponents[k].height;
                }
              }
            }
          }

          var el = this.$refs["zone-" + i][0].$el;

          el.style.minWidth = zone.minWidth;
          el.style.maxWidth = zone.maxWidth;
          el.style.minHeight = zone.minHeight;
          el.style.maxHeight = zone.maxHeight;
          el.style.height = zone.height;
          el.style.width = zone.width;
        }
      });
    },
    getHardwares: function () {
      this.loading = true;
      axios
        .get("/api/hardwares")
        .then((res) => {
          this.hardwares = res.data;
          this.loading = false;
          if (this.ui != undefined) this.applyCurrentInterface();
        })
        .finally((res) => {
          this.loading = false;
        });
    },
  },

  watch: {
    ui: function (newui) {
      console.log("ui change");
      console.log(newui);
      this.applyCurrentInterface();
    },
    equipment: function (newEquipment) {
      console.log("new hardware");
    },
  },
};
</script>
