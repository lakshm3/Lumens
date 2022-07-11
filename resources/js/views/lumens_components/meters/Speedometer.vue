
<template>
  <resizable-card
    dim="%"
    keepRatio="false"
    class="speedometer"
    autoheight="true"
    @onresize="handleResize"
    :minWidth="getMinWidth()"
    minHeight="130px"
  >
    <template v-slot:content>
      <speedometer-graph
        v-if="type==0"
        :value="value"
        :valueForColor="valueForColor"
        :invertedColor="invertedColor"
        :unit="unit"
        :minWidth="width"
        :definition="definition"
        :label="title"
        :max="max"
        :color="color"
      ></speedometer-graph>
      <speedometer-graph-quarter
        v-else
        :value="value"
        :valueForColor="valueForColor"
        :invertedColor="invertedColor"
        :definition="definition"
        :label="title"
        :unit="unit"
        :minWidth="width"
        :max="max"
        :color="color"
      ></speedometer-graph-quarter>
      <slot name="extra"></slot>
    </template>
  </resizable-card>
</template>

<style lang="scss" scoped>
.speedometer {
  padding: 20px;
  padding: 20px;
}

.speedometer-definition {
  padding: 10px;
  border: 1px solid rgba(255, 255, 255, 0.658);
  text-align: left;
  margin-top: 10px;
  font-size: 0.9em;
  font-weight: 200;
  border-radius: 2px;
}
</style>
<script>
export default {
  data() {
    return {
      width: 150,
    };
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.handleResize(this.$el.offsetWidth, this.$el.offsetHeight);
    });
  },
  methods: {
    handleResize: function (w, h) {
      this.width = w - 40;
    },

    handleResizeParent: function (w, h) {
      this.width = w - 40;
    },

    getMinWidth: function () {
      if (this.minWidth == undefined) return 150 + "px";
      else return this.minWidth;
    },
  },
  props: [
    "value",
    "valueForColor",
    "invertedColor",
    "radius",
    "unit",
    "title",
    "minWidth",
    "type",
    "color",
    "max",
    "definition",
  ],
};
</script>