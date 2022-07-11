<template>
  <div class="toggle" @click="onClickToggle">
    <span class="text" :ref="'first'" v-text="textOn"></span>
    <span class="text" :ref="'second'" v-text="textOff"></span>
    <span class="indicator" :style="style"></span>
  </div>
</template>

<style lang="scss" scoped>
.toggle {
  border-radius: 4px;
  height: 42px;
  background: #3b3b3b;
  position: relative;
  display: inline-block;
  overflow: hidden;
  cursor: pointer;
  box-sizing: border-box;

  z-index: 1;
  .text {
    color: white;
    z-index: 1;
    padding: 8px 12px;
    display: inline-block;
    box-sizing: border-box;
    line-height: 25px;
  }
  .indicator {
    position: absolute;
    top: 0px;
    left: 0px;
    height: 100%;
    background-color: #777777;
    transition: 0.3s;
    z-index: -1;
  }
}
</style>

<script>
export default {
  props: ["colorOn", "colorOff", "textOn", "textOff", "value"],
  data() {
    return {
      style: {},
    };
  },
  mounted() {
    this.updateStyle();
  },
  methods: {
    updateStyle: function () {
      this.style = {
        background: this.value ? this.colorOn : this.colorOff,
        left: this.value ? "0px" : this.$refs["second"].offsetLeft + "px",
        width: this.value
          ? this.$refs["first"].offsetWidth + "px"
          : this.$refs["second"].offsetWidth + "px",
      };
    },
    onClickToggle: function () {
      this.$emit("input", !this.value);
      this.$nextTick(() => {
        this.updateStyle();
      });
    },
  },

  watch: {
    value: function (newValue) {
      this.updateStyle();
    },
  },
};
</script>