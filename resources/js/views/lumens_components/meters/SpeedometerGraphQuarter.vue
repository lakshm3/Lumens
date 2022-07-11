<template>
  <div
    class="speedometer-graph-quarter meter"
    :style="{
      width: width,
      height: height,
      minWidth: '120px',
      minHeight: '60px',
    }"
  >
    <div
      class="graphic-element"
      style="position: relative; display: inline; font-size: 0"
    >
      <svg
        class="progress-ring"
        :width="getMinWidth()"
        :height="getMinWidth() / 2"
      >
        <defs>
          <filter
            id="dropshadow"
            x="-80%"
            y="-80%"
            width="280%"
            height="280%"
            filterUnits="userSpaceOnUse"
          >
            <feGaussianBlur in="SourceGraphic" stdDeviation="3" />
            <!-- stdDeviation is how much to blur -->
            <feOffset dx="15" dy="15" result="offsetblur" />
            <feOffset dx="-15" dy="-15" result="offsetblur" />
            <feMerge>
              <feMergeNode />
              <feMergeNode in="SourceGraphic" />
              <feMergeNode in="SourceGraphic" />
            </feMerge>
          </filter>
        </defs>

        <circle
          class="progress-ring__circle"
          stroke="#FFFFFF33"
          :stroke-width="strokeWidth"
          fill="transparent"
          :r="getRadius()"
          :cx="getHalfMinWidth()"
          :cy="0"
          :style="{
            'stroke-dashoffset': getFinalValue(),
            strokeDasharray: getRadius() * 2 * Math.PI,
          }"
        />

        <circle
          v-for="i in 7"
          :key="i"
          class="progress-ring__circle"
          :stroke="getQuarterColor(6 - i)"
          :stroke-width="strokeWidth"
          fill="transparent"
          :r="getRadius()"
          :cx="getHalfMinWidth()"
          :cy="0"
          :style="{
            'stroke-dashoffset': getPathCircle(7 - i),
            strokeDasharray: getRadius() * 2 * Math.PI,
          }"
        />
      </svg>
      <div
        class="tick_container"
        v-bind:style="{
          transform: 'rotate(' + getPercentValue() * 180 + 'deg)',
        }"
      >
        <span class="tick"></span>
      </div>
    </div>

    <div class="measure">
      <div class="value" v-text="value"></div>
      <div class="unit" v-text="unit"></div>
    </div>

    <div
      ref="label"
      class="label"
      style="margin-top: 20px"
      v-if="!hideLabel"
      v-text="label"
    ></div>
    <div
      class="definition"
      v-html="definition"
      v-if="definition && definition.length > 0"
    ></div>
  </div>
</template>

<script>
import ResizeObserver from "resize-observer-polyfill";

export default {
  data() {
    return {
      strokeWidth: 12,
      clientWidth: 125,
      clientHeight: 125,
      labelSize: 25,
    };
  },

  mounted() {
    this.$nextTick(() => {
      this.clientWidth = this.$el.clientWidth;
      this.clientHeight = this.$el.clientHeight;
    });
    /*
    window.addEventListener("resize", () => {
      this.clientWidth = this.$el.clientWidth;
      this.clientHeight = this.$el.clientHeight;
    });*/
    this.resizeObserver = new ResizeObserver((entries, observer) => {
      this.onResize();
    });
    this.resizeObserver.observe(this.$el)
  },

  methods: {
    onResize(e) {
      this.$nextTick(()=>{

      this.clientWidth = this.$el.clientWidth;
      this.clientHeight = this.$el.clientHeight;
      console.log(
        "On Resize ! width => " +this.clientWidth + "" 
      );
      })
    },
    getPercentValue: function () {
      if (this.max == undefined) return this.value / 100;
      return this.value / this.max;
    },
    getQuarterColor: function (i) {
      if (this.color != undefined && this.color != "") return this.color;
      if (this.invertedColor) i = 5 - i;
      if (i == 0) return "#b9261c";
      if (i == 1) return "#f37021";
      if (i == 2) return "#ffc627";
      if (i == 3) return "#6d9c23";
      if (i == 4) return "#7eb338";
      if (i == 5) return "#2d8431";
    },

    getPathCircle: function (i) {
      return this.getRadius() * 2 * Math.PI * (1 - (0.5 * i) / 6);
    },

    getFinalValue: function () {
      return this.getRadius() * 2 * Math.PI * (1 - 50 / 100);
    },
    getProgressValue: function () {
      var value = this.value;
      if (this.valueForColor != undefined) value = this.valueForColor;
      return (
        this.getRadius() * 2 * Math.PI * (1 - (this.value * 80) / 100 / 100)
      );
    },
    getRadius: function () {
      return this.getHalfMinWidth() - this.strokeWidth / 2;
    },

    estimateWidth(value) {
      if (typeof value == "number") {
        return value;
      }
      if (value.indexOf("px") != -1) {
        return value.replace("px", "");
      }
      return (this.clientWidth * parseFloat(value.replace("%", ""))) / 100.0;
    },
    estimateHeight(value) {
      if (typeof value == "number") return value;
      if (value.indexOf("px") != -1) {
        return value.replace("px", "");
      }
      return (this.clientHeight * parseFloat(value.replace("%", ""))) / 100.0;
    },
    getMinWidth: function () {
      var x = 125;
      var y = 125;

      if (this.width) x = this.width;
      if (this.height) y = this.height;
      return Math.min(
        this.estimateWidth(x),
        this.estimateHeight(y) * 2 -
          (this.$refs["label"] != undefined
            ? this.$refs["label"].clientHeight
            : 0)
      );
    },
    getHalfMinWidth: function () {
      return this.getMinWidth() / 2;
    },
  },
  props: [
    "value",
    "valueForColor",
    "radius",
    "unit",
    "width",
    "height",
    "max",
    "color",
    "invertedColor",
    "label",
    "hideLabel",
    "definition",
  ],
  watch: {
    width: function () {
      this.onResize();
    },
  },
};
</script>