<template>
  <div class="labelmeter meter" v-on:click="$emit('click')">
    <div class="labelmeter-title" v-if="!hideLabel" v-text="label"></div>

    <div
      class="labelmeter-badge"
      style
      :style="{
        'background-color': getColor(),
        width: this.badgeWidth == undefined ? '60px' : this.badgeWidth,
      }"
    >
      <div v-if="nodata" class="labelmeter-value">0</div>
      <div
        v-else
        class="labelmeter-value"
        v-text="value + '' + this.getUnit()"
      ></div>
    </div>

    <div
      class="definition"
      v-html="definition"
      v-if="definition && definition.length > 0"
    ></div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },


  methods: {
    getUnit: function () {
      if (this.unit == undefined) return "";
      return this.unit;
    },
    getColor: function () {
      if (this.nodata != undefined && this.nodata) {
        return "#555555";
      }
      if (this.color && this.color!='' && this.color!=null && this.color!=undefined) return this.color;

      if (
        this.thresholds_danger != undefined &&
        this.value >= this.thresholds_danger[0] &&
        this.value < this.thresholds_danger[1]
      )
        return "rgb(" + 225 + "," + 0 + ",0)";
      else if (
        this.thresholds_warn != undefined &&
        this.value >= this.thresholds_warn[0] &&
        this.value < this.thresholds_warn[1]
      )
        return "#db4213";
      else return "rgb(" + 0 + "," + 200 + ",0)";
    },
  },
  props: [
    "value",
    "thresholds_danger",
    "thresholds_warn",
    "unit",
    "label",
    "color",
    "width",
    "height",
    "hideLabel",
    "badgeWidth",
    "nodata",
    "definition",
  ],
};
</script>
