
<template>
  <div
    class="speedometer-graph meter"
    :style="{
      width: width,
      height: height,
      minWidth: '90px',
      minHeight: '90px',
    }"
  >
    <div style="display: block; position: relative">
      <svg class="progress-ring" :width="getMinWidth()" :height="getMinWidth()">
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
          stroke-width="22"
          fill="transparent"
          :r="getRadius()"
          :cx="getHalfMinWidth()"
          :cy="getHalfMinWidth()"
          :style="{
            'stroke-dashoffset': getFinalValue(),
            strokeDasharray: getRadius() * 2 * Math.PI,
          }"
        />

        <circle
          class="progress-ring__circle"
          :stroke="getProgressColor()"
          stroke-width="22"
          fill="transparent"
          :r="getRadius()"
          :cx="getHalfMinWidth()"
          :cy="getHalfMinWidth()"
          :style="{
            'stroke-dashoffset': getProgressValue(),
            //'filter':'url(#dropshadow)',
            strokeDasharray: getRadius() * 2 * Math.PI,
          }"
        />
      </svg>
      <div class="measure">
        <div class="value" v-text="value"></div>
        <div class="unit" v-text="unit"></div>
      </div>
    </div>

    <div
      class="label"
      style="margin-top: -20px"
      ref="label"
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
      console.log(
        "On Resize ! width => " +this.clientWidth + "" 
      );
    });

    this.resizeObserver = new ResizeObserver((entries, observer) => {
      this.onResize();
    });
    this.resizeObserver.observe(this.$el);
  },

  methods: {
    onResize(e) {
      this.clientWidth = this.$el.clientWidth;
      this.clientHeight = this.$el.clientHeight;
    },
    getProgressColor: function () {
      if (this.color != undefined) return this.color;

      var v = this.getPercentValue();
      return "rgb(" + (255 - v * 255) + "," + (0 + v * 255) + ",0)";
    },
    getFinalValue: function () {
      return this.getRadius() * 2 * Math.PI * (1 - 80 / 100);
    },
    getProgressValue: function () {
      return (
        this.getRadius() *
        2 *
        Math.PI *
        (1 - (this.getPercentValue() * 80) / 100)
      );
    },
    getPercentValue: function () {
      if (this.max == undefined) return this.value / 100;
      return this.value / this.max;
    },
    getRadius: function () {
      return (this.getMinWidth() - 22) / 2;
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

      return Math.min(this.estimateWidth(x), this.estimateHeight(y));
    },
    getHalfMinWidth: function () {
      return this.getMinWidth() / 2;
    },
  },
  props: [
    "value",
    "radius",
    "unit",
    "width",
    "height",
    "color",
    "max",
    "nodata",
    "label",
    "hideLabel",
    "definition",
  ],
};
</script>