<template>
  <resizable-card
    :editable="editable"
    @onresize="onResize"
    :resizable="editable"
  >
    <template v-slot:options>
      <v-row no-gutters justify="end" align="end">
        <v-col md="auto" cols="auto" xl="auto">
          <v-btn-toggle color="primary" v-model="section.align" mandatory>
            <v-btn x-small>
              <v-icon x-small>mdi-format-align-left</v-icon>
            </v-btn>

            <v-btn x-small>
              <v-icon x-small>mdi-format-align-center</v-icon>
            </v-btn>

            <v-btn x-small>
              <v-icon x-small>mdi-format-align-right</v-icon>
            </v-btn>
          </v-btn-toggle>
          <v-btn
            outlined
            x-small
            :title="is_auto ? 'Height is automatic' : 'Heiht is a fixed size'"
            @click="setAuto"
          >
            {{ is_auto ? "AUTO" : "FIXED" }}
          </v-btn>

          <v-btn x-small :disabled="!canMoveUp" icon @click="$emit('moveUp')"
            ><v-icon>upload</v-icon></v-btn
          >
          <v-btn
            x-small
            :disabled="!canMoveDown"
            icon
            @click="$emit('moveDown')"
            ><v-icon>download</v-icon></v-btn
          >

          <v-btn x-small icon @click="$emit('delete')"
            ><v-icon>delete</v-icon></v-btn
          >
        </v-col>
      </v-row>
    </template>
    <template v-slot:content>
      <v-container
        fluid
        class="section pl-4 pr-4"
        style="height: 100%"
        :class="{ editable: editable }"
        :style="{ overflow: editable ? 'visible' : '' }"
      >
        <v-row :justify="getAlign()">
          <template v-for="(item, i) in section.items">
            <component-section
              :key="'subsection-' + i"
              v-if="item.type == 'section'"
              style="height: 100%; padding: 0px"
              v-model="section.items[i]"
              :editable="editable"
              :can-move-up="i > 0"
              :can-move-down="i < section.items.length - 1"
              @moveUp="i > 0 ? section.items.move(i, i - 1) : ''"
              @moveDown="
                i < section.items.length - 1 ? section.items.move(i, i + 1) : ''
              "
              @addMeter="onAddMeter"
              @editMeter="onEditMeter"
              @addChart="onAddChart"
              @editChart="onEditChart"
              @delete="section.items.splice(i, 1)"
              :engine="engine"
              @measureSelected="onMeasureSelected"
            ></component-section>

            <resizable-card
              v-if="item.type == 'title'"
              :key="'subsection-' + i"
              :editable="editable"
              default-width="100%"
              :style="{ padding: !editable ? '0px' : '' }"
              class="pt-0 pb-0"
            >
              <template v-slot:options>
                <v-row no-gutters justify="end" align="end">
                  <v-col md="auto" cols="auto" xl="auto">
                    <v-btn-toggle
                      color="primary"
                      v-if="item.style"
                      v-model="item.style.size"
                      mandatory
                    >
                      <v-btn x-small icon>h1</v-btn>
                      <v-btn x-small icon>h2</v-btn>
                      <v-btn x-small icon>h3</v-btn>
                      <v-btn x-small icon>h4</v-btn>
                    </v-btn-toggle>

                    <v-btn
                      x-small
                      :disabled="i == 0"
                      icon
                      @click="section.items.move(i, i - 1)"
                      ><v-icon>upload</v-icon></v-btn
                    >
                    <v-btn
                      x-small
                      :disabled="i == section.items.length - 1"
                      icon
                      @click="section.items.move(i, i + 1)"
                      ><v-icon>download</v-icon></v-btn
                    >
                    <v-btn x-small icon @click="section.items.splice(i, 1)"
                      ><v-icon>delete</v-icon></v-btn
                    >
                  </v-col>
                </v-row>
              </template>
              <template v-slot:content>
                <v-text-field
                  :key="'section-' + i"
                  v-model="item.title"
                  v-if="editable"
                  placeholder="Type your title here"
                  class="pt-0 mt-0"
                  hide-details=""
                  dense
                />
                <div
                  v-else
                  class="card-title"
                  v-text="item.title"
                  :style="{
                    'font-size':
                      1.3 - (item.style ? item.style.size * 0.1 : 0) + 'em',
                  }"
                ></div>
              </template>
            </resizable-card>
            <resizable-card
              v-if="item.type == 'meter'"
              :editable="editable"
              width="190px"
              @mouseover.native="
                showHelp(
                  item.meter.indicator ? item.meter.indicator : '',
                  $event
                )
              "
              @mouseleave.native="hideHelp()"
              :key="'displayed-meter-' + i"
            >
              <template v-slot:options>
                <v-row no-gutters justify="end" align="end">
                  <v-col md="auto" cols="auto" xl="auto">
                    <v-btn
                      x-small
                      :disabled="i == 0"
                      icon
                      @click="section.items.move(i, i - 1)"
                      ><v-icon>upload</v-icon></v-btn
                    >
                    <v-btn
                      x-small
                      :disabled="i == section.items.length - 1"
                      icon
                      @click="section.items.move(i, i + 1)"
                      ><v-icon>download</v-icon></v-btn
                    >
                    <v-btn
                      x-small
                      icon
                      @click="$emit('editMeter', section, item)"
                      ><v-icon>edit</v-icon></v-btn
                    >

                    <v-btn
                      x-small
                      icon
                      @click="
                        section.items.splice(
                          i,
                          0,
                          JSON.parse(JSON.stringify(item))
                        )
                      "
                      ><v-icon>file_copy</v-icon></v-btn
                    >
                    <v-btn x-small icon @click="section.items.splice(i, 1)"
                      ><v-icon>delete</v-icon></v-btn
                    >
                  </v-col>
                </v-row>
              </template>
              <template v-slot:content>
                <component
                  class="mx-auto"
                  width="100%"
                  height="100%"
                  :size="item.meter.size"
                  :value="
                    getComputedFloatMeasure(
                      item.meter.indicator,
                      item.meter.displayedValue,
                      item.meter.factor,
                      item.meter.nbDecimal
                    )
                  "
                  :valueForColor="
                    item.meter.coloredValue
                      ? getFloatMeasure(
                          item.meter.indicator,
                          item.meter.coloredValue,
                          item.meter.nbDecimal
                        )
                      : undefined
                  "
                  :is="item.meter.meterType"
                  :unit="item.meter.unit"
                  :label="
                    item.meter.indicator
                      ? $t('indicators.' + item.meter.indicator)
                      : 'Titre'
                  "
                  :hide-label="!item.meter.showLabel"
                  :color="
                    hasMeasure(item.meter.indicator, 'value')
                      ? item.meter.color
                        ? typeof item.meter.color === 'string'
                          ? item.meter.color
                          : item.meter.color.hexa
                        : undefined
                      : '#555555'
                  "
                  :definition="item.meter.definition"
                  :max="item.meter.max"
                  :invertedColor="item.meter.invertedColor"
                ></component>
              </template>
            </resizable-card>
            <resizable-card
              v-if="item.type == 'chart'"
              :editable="editable"
              :keepRatio="false"
              autoheight
              :key="'displayed-chart-' + i"
            >
              <template v-slot:options>
                <v-row no-gutters justify="end" align="end">
                  <v-col md="auto" cols="auto" xl="auto">
                    <v-btn
                      x-small
                      icon
                      @click="$emit('editChart', section, item)"
                      ><v-icon>edit</v-icon></v-btn
                    >
                    <v-btn
                      x-small
                      icon
                      @click="
                        section.items.splice(
                          i,
                          0,
                          JSON.parse(JSON.stringify(item))
                        )
                      "
                      ><v-icon>file_copy</v-icon></v-btn
                    >
                    <v-btn x-small icon @click="section.items.splice(i, 1)"
                      ><v-icon>delete</v-icon></v-btn
                    >
                  </v-col>
                </v-row>
              </template>
              <template v-slot:content>
                <component-chart
                  class="mx-auto"
                  width="100%"
                  height="100%"
                  :engine="engine"
                  @measureSelected="onMeasureSelected"
                  v-model="item.chart"
                ></component-chart>
              </template>
            </resizable-card>
          </template>
        </v-row>
        <v-row v-if="editable" justify="center" align="center">
          <v-col>
            <div v-ripple class="new_section" @click="addTitle()">
              <v-icon class="mr-2">mdi-format-title</v-icon> NEW TITLE
            </div>
          </v-col>
          <v-col>
            <div v-ripple class="new_section" @click="$emit('addMeter', value)">
              <v-icon class="mr-2">mdi-speedometer</v-icon> NEW METER
            </div>
          </v-col>
          <v-col>
            <div v-ripple class="new_section" @click="$emit('addChart', value)">
              <v-icon class="mr-2">mdi-chart-box-plus-outline</v-icon> NEW CHART
            </div>
          </v-col>
          <v-col>
            <div v-ripple class="new_section" @click="addSubSection()">
              <v-icon class="mr-2">mdi-select-group</v-icon> NEW SECTION
            </div>
          </v-col>
        </v-row>
      </v-container>

      <help-poppup
        v-if="helpVisible && !editable"
        :help="help"
        :top="helpTop"
        :left="helpLeft"
      ></help-poppup>
    </template>
  </resizable-card>
</template>
<style scoped>
.section.editable {
  border: 3px solid rgba(255, 255, 255, 0.1);
}

.clickable {
  cursor: pointer;
  border: dashed 3px #fff;
}
.new_section {
  border: dashed 3px #fff;
  padding: 15px 10px;
  text-align: center;
  cursor: pointer;
}
.new_section:hover {
  background: rgba(255, 255, 255, 0.137);
}
.card .card-title {
  margin-bottom: 0px;
}
</style>
<script>
import DashboardComponentUtility from "./DashboardComponentUtility.js";

export default {
  mixins: [DashboardComponentUtility],
  props: ["value", "editable", "canMoveUp", "canMoveDown", "engine"],
  data() {
    return {
      section: {
        align: 0,
        items: [],
      },
      is_auto: true,
    };
  },
  mounted() {
    this.section = this.value;
  },
  methods: {
    getComputedFloatMeasure(key, displayedValue, factor, nbDecimal) {
      if (displayedValue == "100% - valueInPercent") {
        return 100 - this.getFloatMeasure(key, "valueInPercent", nbDecimal);
      }
      return this.getFloatMeasure(
        key,
        displayedValue,
        nbDecimal,
        factor
      );
    },
    onMeasureSelected(a, b, c) {
      this.$emit("measureSelected", a, b, c);
    },
    getAlign() {
      if (this.section.align == 0) return "start";
      if (this.section.align == 1) return "center";
      if (this.section.align == 2) return "end";
    },
    onResize: function (w, h) {
      this.$emit("onresize", w, h);
      this.is_auto =
        this.$el.style.height == "auto" || this.$el.style.height == "";
    },
    hasItemNotSection() {
      for (var i = 0; i < this.value.items.length; i++) {
        if (this.value.items[i].type != "section") return true;
      }
      return false;
    },
    addTitle() {
      this.section.items.push({
        title: "",
        type: "title",
        style: { size: "h1" },
      });
      this.$emit("input", this.section);
    },
    onAddMeter(s) {
      this.$emit("addMeter", s);
    },
    onEditMeter(section, meter) {
      this.$emit("editMeter", section, meter);
    },
    onAddChart(s) {
      this.$emit("addChart", s);
    },
    onEditChart(section, meter) {
      this.$emit("editChart", section, meter);
    },
    addSubSection() {
      this.section.items.push({
        type: "section",
        items: [],
      });

      this.$emit("input", this.section);
    },

    setAuto: function () {
      this.$el.style.height = "auto";
      this.is_auto = true;
    },
  },
  watch: {
    value: function (newValue) {
      this.section = newValue;
    },
  },
};
</script>