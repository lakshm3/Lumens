<template>
  <div
    class="circularmeter meter"
    :style="{ width: this.getSize() + 'px', height: this.getSize() + 'px' }"
  >
    <svg style="width: 100%; height: 100%">
      <circle
        class="progress-ring__circle"
        :stroke="getColor()"
        :stroke-width="strokeWidth"
        fill="transparent"
        :r="getRadius()"
        :cx="getSize() / 2"
        :cy="getSize() / 2"
        :style="{
          'stroke-dashoffset': -getProgressValue(),
          strokeDasharray: getRadius() * 2 * Math.PI,
        }"
      />
    </svg>

    <div class="pastille" :style="{ 'background-color': getColor() }"></div>

    <div
      class="value"
      v-text="parseInt(getPercentValue() * 100) + this.getUnit()"
    ></div>

    <div
      class="label"
      v-if="!hideLabel"
      v-text="label"
      style="margin-left: -5px; margin-right: -5px"
    ></div>

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
    return {
      strokeWidth: 3,
    };
  },

  methods: {
    getSize: function () {
      if(this.size == undefined) return 40;
      //if(this.$el != undefined) return this.$el.offsetWidth;
      return 40 + (this.size - 3) * 5;
    },

    getUnit: function () {
      if (this.unit == undefined) return "%";
      return this.unit;
    },

    getProgressValue: function () {
      return this.getRadius() * 2 * Math.PI * (1 - this.getPercentValue());
    },
    getRadius: function () {
      return (this.getSize() - 8) / 2;
    },

    getPercentValue: function () {
      return this.value / this.max;
    },

    getColor: function () {
      if (this.disabled != undefined && this.disabled)
        return "rgb(100,100,100)";

      if (this.color) return this.color;
      if (
        this.thresholds_danger != undefined &&
        this.value >= this.thresholds_danger[0] &&
        this.value < this.thresholds_danger[1]
      )
        return "rgb(" + 225 + "," + 0 + ",0)";
      else if (
        this.thresholds_danger != undefined &&
        this.value >= this.thresholds_warn[0] &&
        this.value < this.thresholds_warn[1]
      )
        return "rgb(219, 66, 19)";
      else return "rgb(" + 0 + "," + 200 + ",0)";
    },
  },
  props: [
    "value",
    "thresholds_danger",
    "thresholds_warn",
    "unit",
    "max",
    "disabled",
    "label",
    "hideLabel",
    "color",
    "size",
    "definition"
  ],
};
</script>
