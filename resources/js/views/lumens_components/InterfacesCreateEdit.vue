<template>
  <v-sheet style="width:100% position:relative">
    <div class="overflow-hidden fill-height;" style="width: 100vw">
      <div
        class="view fill-height pt-12"
        :style="{
          position: 'absolute',
          'overflow-y': 'auto',
          left: !expanded ? '0px' : '300px',
          width: !expanded ? '100%' : 'calc( 100% - 300px )',
        }"
      >
        <loader v-if="loading"></loader>
        <dashboard-component
          class="engine dropable_zone"
          default-width="50%"
          v-for="(zone, i) in zones"
          :key="i"
          :class="{
            selected: zoneSelected == zone && editable,
            preview: !editable,
          }"
          :editable="editable"
          :can-move-up="i > 0"
          :can-move-down="i < zones.length - 1"
          @moveUp="moveUp(i)"
          @moveDown="moveDown(i)"
          @delete="removeZone(i)"
          @duplicate="duplicateZone(zone)"
          @click.native="selectZone(zone)"
          :ref="'zone-' + i"
        >
          <template v-slot:header v-if="editable">
            <v-row style="padding: 10px">
              <v-col>
                <v-select
                  filled
                  label="Zone type"
                  v-model="zone.type"
                  hide-details=""
                  hide-selected
                  :items="[
                    { id: 'vibox', value: 'Vibox' },
                    { id: 'other', value: 'Others' },
                  ]"
                  item-text="value"
                  item-value="id"
                >
                </v-select>
              </v-col>
              <v-col>
                <!--
                <v-text-field
                  filled
                  type="number"
                  hide-details=""
                  hide-selected
                  label="Engine Number"
                  v-model="zone.engine_number"
                  min="1"
                  step="1"
                  max="10"
                /> -->

                <v-select
                  filled
                  hide-details=""
                  label="Process"
                  :value="parseInt(zone.engine_number)"
                  @input="zone.engine_number = parseInt(arguments[0])"
                  :items="engines"
                  :item-text="
                    (i) =>
                      i.name +
                      ' - ' +
                      i.process_type +
                      ' (' +
                      i.engine_vib_id +
                      ')'
                  "
                  item-value="id"
                />
              </v-col>
              <v-col>
                <v-text-field
                  filled
                  type="text"
                  hide-details=""
                  hide-selected
                  label="Zone name"
                  v-model="zone.name"
                  placeholder="Starboard, Portside, ..."
                />
              </v-col>
            </v-row>
          </template>
          <template v-slot:content>
            <h2
              v-if="!editable"
              style="margin: 0px 10px"
              v-text="zone.name"
            ></h2>
            <component
              v-for="(component, j) in zone.components"
              :key="j"
              :ref="'component-' + i + '-' + j"
              :editable="editable"
              :extras="component.extras"
              @extras-changed="onExtrasChanged(i, j, ...arguments)"
              v-on:delete="deleteComponent(zone, j)"
              v-on:duplicate="duplicateComponent(i, j)"
              :is="getComponentByTitle(component.name)"
              @moveUp="j > 0 ? zone.components.move(j, j - 1) : ''"
              @moveDown="
                j < zone.components.length - 1
                  ? zone.components.move(j, j + 1)
                  : ''
              "
            ></component>
          </template>
        </dashboard-component>
      </div>
      <v-navigation-drawer
        class="components fill-height"
        width="300px"
        absolute
        v-model="expanded"
      >
        <v-card class="pa-4" tile>
          <v-card-title>
            <v-btn left icon @click="expanded = !expanded"
              ><v-icon>mdi-arrow-left</v-icon></v-btn
            >
            Setup your interface</v-card-title
          >

          <v-card-text>
            <v-text-field
              filled
              label="Name"
              type="text"
              v-model="ui.name"
              value
              placeholder="Name of your interface"
            />

            <h3>Visibility</h3>
            <select v-model="ui.public">
              <option :value="true">public</option>
              <option :value="false">private</option>
            </select>
            <p v-if="ui.public">
              All the users of the platform would be allow, to use that
              interface to visualise data from their equipments, but, they won't
              be able to edit the interface
            </p>
            <p v-else>
              Only you can use that interface to visualise data from your
              equipments
            </p>

            <button @click="addZone" class="secondary button">
              ADD A NEW ZONE
            </button>

            <div class="components_list">
              <br />
              <h3>Components</h3>
              <div
                class="component"
                v-for="(component, i) in components"
                :key="i"
                v-text="component"
                @click="addComponent(component)"
              ></div>

              <br />

              <br />
            </div>
            <br />

            <button @click="saveInterface(false)" class="save_interface button">
              SAVE YOUR INTERFACE
            </button>
          </v-card-text>
        </v-card>
      </v-navigation-drawer>

      <v-btn
        v-show="!expanded && editable"
        fab
        @click="expanded = !expanded"
        style="position: absolute; left: 20px; bottom: 20px; z-index: 1"
        color="primary"
      >
        <v-icon>mdi-arrow-expand-right</v-icon>
      </v-btn>
      <v-btn
        fab
        @click="
          editable = !editable;
          expanded = editable;
        "
        style="position: absolute; right: 20px; bottom: 20px; z-index: 1"
        color="primary"
        :title="editable ? 'Preview mode' : 'Edit mode'"
      >
        <v-icon>{{ editable ? "visibility" : "edit" }}</v-icon>
      </v-btn>
    </div>
  </v-sheet>
</template>

<style lang="scss" scoped>
.engine.preview {
  border-width: 0px;
}
label {
  margin-left: 10px;
  margin-right: 10px;
}

.components {
  text-align: left;
  box-sizing: border-box;
  background-color: #303030;
  height: 100vh;

  input {
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 15px;
  }

  .component {
    padding: 10px;
    border: 2px solid white;
    margin-bottom: 20px;
    transition: 0.3s;
    cursor: pointer;
  }

  .component:hover {
    background-color: rgba(255, 255, 255, 0.384);
  }

  a {
    color: white;
  }

  .collapse {
    display: inline-block;
    margin-top: 20px;
  }

  .icon {
    padding: 15px 10px;
    transition: 0.3s;
    cursor: pointer;
    vertical-align: middle;
    border-radius: 1px;
  }
  .icon:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
}

select {
  padding: 10px;
  margin-bottom: 10px;
  margin-left: 5px;
  background-color: transparent;
  border: 2px solid white;
  color: white;
  option {
    background-color: #303030;
  }
}

h3 {
  text-transform: uppercase;
}
.dropable_zone {
  border: 2px dashed white;
  width: 300px;
  min-height: 200px;
}
.button {
  padding: 12px 10px;
  margin-top: 5px;
  cursor: pointer;
  border: 0px;
  outline: none;
  border-radius: 1px;
  width: 100%;
  font-weight: 500;
}
</style>
<script>
import components from "./dashboard_components";
import DashboardComponentUtility from "./DashboardComponentUtility.js";

export default {
  components: components,

  mixins: [DashboardComponentUtility],
  data() {
    return {
      engines: [],
      expanded: true,
      editable: true,
      zones: [{ engine: -1, components: [] }],
      zoneSelected: {},
      loading: false,
      ui: { public: false },
      components: [
        "Engine Diagnostic",
        "Engine Speed & Consumption",
        "Specific Fuel Consumption",
        "Cylinder Max Pressure",
        "Shaft Diagnostic",
        "Oil Condition",
        "EDS Indicator",
        "Trends Analysis",
        "Localisation Map",
        "Turbine",
        "Torque",
        "RotatingMachine",
        "Compressor",
        "Hardwares Summary",
        "Generic Component",
      ],
    };
  },

  mounted() {
    if (this.$route.params.interface_id != undefined) this.getInterface();

    this.zoneSelected = this.zones[0];
    this.getEngines();
  },

  methods: {
    onExtrasChanged(i, j, extras) {
      this.$set(this.zones[i].components[j], "extras", extras);
    },
    selectZone: function (zone) {
      this.zoneSelected = zone;
    },
    getInterface: function () {
      this.loading = true;
      var id = this.$route.params.interface_id;
      axios
        .get("/api/interfaces/" + id + "")
        .then((res) => {
          this.ui = res.data;
          var config = JSON.parse(this.ui.config);
          this.zones = config.zones;
          this.zoneSelected = this.zones[0];
          this.$nextTick(function () {
            this.applyConfig();
          });

          this.loading = false;
        })
        .catch((err) => {
          console.error(err);
          this.loading = false;
          if (err.response != null && err.response.status == 404) {
            this.$router.push({ name: "setup" });
          }
        });
    },

    moveUp(index) {
      if (index > 0) this.zones.move(index, index - 1);
    },

    moveDown(index) {
      if (index < this.zones.length - 1) this.zones.move(index, index + 1);
    },
    applyConfigForComponent: function (el, component) {
      el.style.width = component.width;
      el.style.minWidth = component.minWidth;
      el.style.maxWidth = component.maxWidth;
      el.style.minHeight = component.minHeight;
      el.style.maxHeight = component.maxHeight;
      el.style.height = component.height;

      var subElts = el.querySelectorAll(".resizable");
      if (subElts != null) {
        for (var k = 0; k < subElts.length; k++) {
          if (component.subcomponents[k] != undefined) {
            subElts[k].style.width = component.subcomponents[k].width;
            subElts[k].style.minWidth = component.subcomponents[k].minWidth;
            subElts[k].style.maxWidth = component.subcomponents[k].maxWidth;
            subElts[k].style.minHeight = component.subcomponents[k].minHeight;
            subElts[k].style.maxHeight = component.subcomponents[k].maxHeight;
            subElts[k].style.height = component.subcomponents[k].height;
          }
        }
      }
    },

    applyConfigForZonePos: function (index) {
      var zone = this.zones[index];
      for (var j = 0; j < zone.components.length; j++) {
        var el = this.$refs["component-" + index + "-" + j][0].$el;
        this.applyConfigForComponent(el, zone.components[j]);
      }

      var el = this.$refs["zone-" + index][0].$el;

      el.style.minWidth = zone.minWidth;
      el.style.maxWidth = zone.maxWidth;
      el.style.minHeight = zone.minHeight;
      el.style.maxHeight = zone.maxHeight;
      el.style.height = zone.height;
      el.style.width = zone.width;
    },

    applyConfig: function () {
      console.log(this.zones);
      console.log(this.$refs);
      var vm = this;
      for (var i = 0; i < this.zones.length; i++) {
        this.applyConfigForZonePos(i);
      }
    },

    addComponent: function (component) {
      this.zoneSelected.components.push({
        name: component,
        extras: {},
      });
    },

    deleteComponent: function (zone, index) {
      zone.components.splice(index, 1);
    },

    duplicateComponent: function (indexZone, indexComponent) {
      this.zones[indexZone].components.push(
        JSON.parse(
          JSON.stringify(this.zones[indexZone].components[indexComponent])
        )
      );

      //on duplique à la fin
      indexComponent = this.zones[indexZone].components.length - 1;

      this.$nextTick(() => {
        var el =
          this.$refs["component-" + indexZone + "-" + indexComponent][0].$el;
        this.applyConfigForComponent(
          el,
          this.zones[indexZone].components[indexComponent]
        );
      });
    },
    addZone: function () {
      this.zones.push({ engine: -1, components: [] });
      this.zoneSelected = this.zones[this.zones.length - 1];
    },

    duplicateZone: function (zone) {
      this.zones.push(JSON.parse(JSON.stringify(zone)));

      this.$nextTick(() => {
        this.applyConfigForZonePos(this.zones.length - 1);
      });
    },
    removeZone: function (i) {
      this.zones.splice(i, 1);
    },
    goInLive: function () {
      this.saveInterface(true);
    },

    getEngines: function () {
      axios.get("/api/engines").then((res) => {
        this.engines = res.data;
      });
    },

    getStyles: function (el) {
      var style = el.style;
      if (style == undefined) return {};
      var arr = {
        width: style.width,
        minWidth: style.minWidth,
        maxWidth: style.maxWidth,
        minHeight: style.minHeight,
        maxHeight: style.maxHeight,
        height: style.height,
      };
      return arr;
    },
    saveInterface: function (goInLive) {
      for (var i = 0; i < this.zones.length; i++) {
        for (var j = 0; j < this.zones[i].components.length; j++) {
          var el = this.$refs["component-" + i + "-" + j][0].$el;
          var styles = this.getStyles(el);
          var component = this.zones[i].components[j];
          this.zones[i].components[j] = { ...component, ...styles };
          var subElts = el.querySelectorAll(".resizable");
          this.zones[i].components[j].subcomponents = [];
          if (subElts != null) {
            for (var k = 0; k < subElts.length; k++) {
              this.zones[i].components[j].subcomponents.push(
                this.getStyles(subElts[k])
              );
            }
          }
        }

        var el = this.$refs["zone-" + i][0].$el;
        var style = el.currentStyle || window.getComputedStyle(el);
        var style = el.style;
        this.zones[i].minWidth = style.minWidth;
        this.zones[i].maxWidth = style.maxWidth;
        this.zones[i].minHeight = style.minHeight;
        this.zones[i].maxHeight = style.maxHeight;
        this.zones[i].width = style.width;
        this.zones[i].height = style.height;
      }
      this.ui.config = JSON.stringify({
        zones: this.zones,
      });

      var promise = {};
      var id = this.ui._id ? this.ui._id : this.ui.id;

      if (id != undefined) {
        promise = axios.put("/api/interfaces/" + id, this.ui);
      } else {
        promise = axios.post("/api/interfaces", this.ui);
      }

      promise.then((res) => {
        //this.$router.push({ name: 'home' })
        this.$router.go(-1);
      });
    },
  },
};
</script>
