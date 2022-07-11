<template>
  <dashboard-component
    class="card generic"
    :editable="editable"
    v-on="$listeners"
    default-width="100%"
    :style="{ 'padding-top': editable ? '' : '5px' }"
  >
    <template v-slot:content>
      <v-container fluid>
        <v-row>
          <template v-for="(section, i) in newExtras.sections">
            <v-col :key="'section-' + i" md="12" cols="12" xl="12">
              <component-section
                style="padding: 0px"
                v-model="newExtras.sections[i]"
                @measureSelected="onMeasureSelected"
                :editable="editable"
                :engine="engine"
                :can-move-up="i > 0"
                :can-move-down="i < newExtras.sections.length - 1"
                @moveUp="i > 0 ? newExtras.sections.move(i, i - 1) : ''"
                @moveDown="
                  i < newExtras.sections.length - 1
                    ? newExtras.sections.move(i, i + 1)
                    : ''
                "
                @addChart="addNewChart"
                @editChart="onEditChart"
                @addMeter="addNewMeter"
                @editMeter="onEditMeter"
                @delete="newExtras.sections.splice(i, 1)"
              >
              </component-section>
            </v-col>
          </template>
        </v-row>
      </v-container>

      <div
        class="new_section mt-4"
        v-ripple
        v-if="editable"
        @click="newExtras.sections.push({ items: [] })"
      >
        ADD A SECTION
      </div>
      <v-dialog
        v-model="addComponentDialog"
        scrollable
        v-if="addComponentDialog"
        max-width="800px"
        persistent
      >
        <v-card tile>
          <v-toolbar flat dark color="primary">
            <v-toolbar-title>{{
              editMeterMode ? "EDIT THE METER" : "ADD A METER"
            }}</v-toolbar-title>
            <v-spacer> </v-spacer>
            <v-btn icon dark @click="addComponentDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <div class="text-h6 mt-4" style="text-align: left">
              1 - Select your base meter
            </div>

            <v-item-group mandatory v-model="meter.meterTypeIndex">
              <v-container fluid>
                <v-row>
                  <v-col
                    v-for="(c, i) in components"
                    :key="'component-' + i"
                    cols="12"
                    md="3"
                  >
                    <v-item v-slot="{ active, toggle }">
                      <v-card
                        :color="active ? 'primary' : ''"
                        class="d-flex align-center pa-4"
                        dark
                        height="150"
                        @click="toggle"
                        style="text-align: center"
                      >
                        <component
                          class="mx-auto"
                          width="100%"
                          height="100%"
                          :is="c"
                          :size="meter.size"
                          :unit="meter.unit"
                          :label="
                            meter.indicator
                              ? $t('indicators.' + meter.indicator)
                              : 'Titre'
                          "
                          :hide-label="!meter.showLabel"
                          :color="
                            meter.color
                              ? typeof meter.color === 'string'
                                ? meter.color
                                : meter.color.hexa
                              : ''
                          "
                          :value="value.toFixed(meter.nbDecimal)"
                          :max="meter.max"
                          :definition="meter.definition"
                          :invertedColor="meter.invertedColor"
                        ></component>
                      </v-card>
                    </v-item>
                  </v-col>
                </v-row>
              </v-container>
            </v-item-group>
            <div class="text-h6 mt-4" style="text-align: left">
              2 - Customize your meter
            </div>
            <v-row class="mt-4">
              <v-col>
                <v-checkbox
                  v-model="meter.showLabel"
                  label="Show Label"
                ></v-checkbox>
              </v-col>
              <v-col>
                <v-text-field v-model="meter.unit" label="Unit" />
              </v-col>
              <v-col>
                <v-text-field v-model="meter.factor" label="Factor" hint="Si la valeur est trop grande, vous pouvez appliquer un facteur pour changer d'unité" />
              </v-col>
              <v-col>
                <v-text-field v-model="meter.max" label="Maximum value" />
              </v-col>
            </v-row>
            <v-slider
              label="Size"
              v-if="meter.meterTypeIndex == 3"
              max="6"
              v-model="meter.size"
              min="0"
            ></v-slider>

            <v-slider
              label="Try the value"
              :max="meter.max"
              v-model="value"
              min="0"
            ></v-slider>

            <v-row no-gutters>
              <v-col v-if="meter.meterTypeIndex == 0">
                <v-checkbox
                  v-model="meter.invertedColor"
                  label="Reverse Direction"
                ></v-checkbox>
              </v-col>
              <v-col>
                <v-checkbox
                  v-model="meter.customColor"
                  @change="!meter.customColor ? (meter.color = null) : ''"
                  label="Custom Color"
                ></v-checkbox>
                <v-color-picker
                  v-if="meter.customColor"
                  hide-inputs
                  v-model="meter.color"
                ></v-color-picker>
              </v-col>
            </v-row>
            <div class="text-h6 mt-4" style="text-align: left">
              3 - Link your indicator
            </div>

            <v-select
              label="Indicator for value"
              v-model="meter.indicator"
              :items="allindicators"
              item-text="name"
              item-value="key"
            >
              <template slot="selection" slot-scope="data">
                {{ $t("indicators." + data.item.key) }}
              </template>
              <template slot="item" slot-scope="data">
                <v-row>
                  <v-col> {{ $t("indicators." + data.item.key) }}</v-col>
                  <v-col> ID: {{ data.item.key }}</v-col>
                </v-row>
              </template>
            </v-select>

            <v-select
              label="Displayed Value"
              v-model="meter.displayedValue"
              :items="['value', 'valueInPercent', '100% - valueInPercent']"
            >
            </v-select>
            <v-select
              label="Colored value (needle)"
              v-model="meter.coloredValue"
              :items="['none', 'value', 'valueInPercent']"
            >
            </v-select>

            <v-textarea
              v-model="meter.definition"
              label="Definition"
            ></v-textarea>
            <v-text-field
              label="Nb Decimal Precision"
              v-model="meter.nbDecimal"
              type="number"
            ></v-text-field>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="secondary" @click="saveMeter()">{{
              editMeterMode ? "Modifier" : "Ajouter"
            }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog
        v-model="addChartDialog"
        scrollable
        v-if="addChartDialog"
        max-width="800px"
        persistent
      >
        <v-card tile>
          <v-toolbar flat dark color="primary">
            <v-toolbar-title>{{
              editChartMode ? "EDIT THE CHART" : "ADD A CHART"
            }}</v-toolbar-title>
            <v-spacer> </v-spacer>
            <v-btn icon dark @click="addChartDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <v-text-field label="Title" v-model="chart.title"></v-text-field>

            <v-select
              v-model="chart.lateralIndicators"
              label="Lateral Indicators"
              item-value="key"
              :items="allindicators"
              persistent-hint
              multiple
              hint="Les indicateurs apparaitront dans l'ordre de sélection de
                    cette liste. Si le nombre d'indicateurs dépasse 5, les
                    excédants seront choisissable au sein d'une liste déroulante"
            >
              <template slot="selection" slot-scope="data">
                {{ $t("indicators." + data.item.key) }}
              </template>
              <template slot="item" slot-scope="data">
                <v-row>
                  <v-col> {{ $t("indicators." + data.item.key) }}</v-col>
                  <v-col> ID: {{ data.item.key }}</v-col>
                </v-row>
              </template>
            </v-select>

            <v-select
              v-model="chart.graphIndicators"
              label="Graph Indicators"
              item-value="key"
              multiple
              :items="allindicators"
              persistent-hint
              hint="Maximum 4 indicateurs"
            >
              <template slot="selection" slot-scope="data">
                {{ $t("indicators." + data.item.key) }}
              </template>
              <template slot="item" slot-scope="data">
                <v-row>
                  <v-col> {{ $t("indicators." + data.item.key) }}</v-col>
                  <v-col> ID: {{ data.item.key }}</v-col>
                </v-row>
              </template>
            </v-select>
            <v-switch
              v-model="chart.comparative_mode"
              label="Add a comparative indicator"
            ></v-switch>
            <v-switch
              v-model="chart.show_rpm_graph"
              label="Show RPM Graph"
            ></v-switch>

            <v-select
              v-if="chart.show_rpm_graph"
              v-model="chart.rpm_indicator"
              label="RPM Indicator"
              item-value="key"
              :items="['ChannelSpeed']"
              return-object
            >
            </v-select>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="secondary" @click="saveChart()">{{
              editChartMode ? "Modifier" : "Ajouter"
            }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </template>
  </dashboard-component>
</template>

<style scoped>
.section {
  border: 2px solid white;
}

.clickable {
  cursor: pointer;
  border: dashed 3px #fff;
}
.new_section {
  display: block;
  border: dashed 3px #fff;
  padding: 25px 20px;
  text-align: center;
  cursor: pointer;
}
.new_section:hover {
  background: rgba(255, 255, 255, 0.137);
}
</style>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";
import DashboardComponent from "../DashboardComponent.vue"

export default {
  mixins: [DashboardComponentUtility],
  components:{DashboardComponent},
  props: ["editable", "extras", "engine"],
  data() {
    return {
      components: [
        "speedometer-graph-quarter",
        "speedometer-graph",
        "labelmeter",
        "circularmeter",
      ],
      addComponentDialog: false,
      addChartDialog: false,
      meterTypeIndex: 0,
      meter: {
        meterType: "",
        unit: "%",
        showLabel: true,
        indicator: null,
        size: 3,
        factor:1,
        coloredValue: "none",
        displayedValue: "none",
        max: 100,
        color: null,
        nbDecimal: 0,
        customColor: false,
        invertedColor: false,
        definition: "",
      },
      chart: {
        rpm_indicator: "ChannelSpeed",
      },
      value: 50,
      newExtras: {
        sections: [],
      },
      currentSection: null,
      editMeterMode: false,
      editChartMode: false,
    };
  },
  mounted() {
    this.newExtras.sections = this.extras.sections;
    if (this.newExtras.sections == undefined) this.newExtras.sections = [];
  },
  methods: {
    onMeasureSelected(a, b, c) {
      this.$emit("measureSelected", a, b, c);
    },
    addNewMeter(section) {
      this.addComponentDialog = true;
      this.editMeterMode = false;
      this.currentSection = section;
    },
    onEditMeter(section, item) {
      this.addComponentDialog = true;
      this.editMeterMode = true;
      this.currentSection = section;
      this.meter = item.meter;
    },
    addNewChart(section) {
      this.addChartDialog = true;
      this.editChartMode = false;
      this.currentSection = section;
    },
    onEditChart(section, item) {
      this.addChartDialog = true;
      this.editChartMode = true;
      this.currentSection = section;
      this.chart = item.chart;
    },
    saveTitle() {
      this.newExtras.sections.push({
        type: "title",
        title: this.title,
      });
      this.$emit("extras-changed", this.newExtras);
    },
    saveChart() {
      if (!this.editChartMode) {
        this.currentSection.items.push({
          type: "chart",
          chart: JSON.parse(JSON.stringify(this.chart)),
        });

        this.addChartDialog = false;
      } else {
        this.addChartDialog = false;
      }
      this.$emit("extras-changed", this.newExtras);
    },

    saveMeter() {
      this.meter.meterType = this.components[this.meter.meterTypeIndex];

      if (!this.editMeterMode) {
        this.currentSection.items.push({
          type: "meter",
          meter: JSON.parse(JSON.stringify(this.meter)),
        });

        this.addComponentDialog = false;
      } else {
        this.addComponentDialog = false;
      }

      this.$emit("extras-changed", this.newExtras);
    },
  },
  watch: {
    extras: function (extrasLoc) {
      this.newExtras.sections = extrasLoc.sections;
      if (this.newExtras.sections == undefined) this.newExtras.sections = [];
    },
    newExtras: {
      handler: function (newExtrasLoc) {
        this.$emit("extras-changed", this.newExtras);
      },
      deep: true,
    },
  },
};
</script>
