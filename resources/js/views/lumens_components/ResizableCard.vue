<template>
  <v-hover v-slot="{ hover }" v-model="hovered">
    <v-col
      md="auto"
      xl="auto"
      cols="auto"
      class="resizable"
      @mouseenter="onMouseEnter"
      @mouseleave="onMouseLeave"
      @mousedown="onMouseDown"
      :class="{
        'move-bottom': moveBottom,
        'move-top': moveTop,
        'move-left': moveLeft,
        'move-right': moveRight,
        noselect: drag,
      }"
      :style="styleObject"
    >
      <div class="options" v-if="hover && editable">
        <slot name="options"> </slot>
      </div>
      <span ref="content"> <slot name="content"> </slot></span>
    </v-col>
  </v-hover>
</template>

<style scoped>
.options {
  position: absolute;
  right: -1px;
  display: inline-block;
  bottom: 100%;
  padding-top: 5px;
  padding-left: 10px;
  padding-right: 10px;
  padding-bottom: 5px;
  background: #343439;
}
</style>
<script>
export default {
  props: [
    "title",
    "dim",
    "keepRatio",
    "minWidth",
    "minHeight",
    "width",
    "autoheight",
    "editable",
    "resizable",
    "defaultWidth",
  ],
  data: function () {
    return {
      wComputed: 0,
      moveBottom: false,
      moveRight: false,
      moveLeft: false,
      moveTop: false,
      drag: false,
      resizeMode: false,
      gap: 15,
      hovered: false,
      ratio: 1,
    };
  },
  computed: {
    styleObject() {
      var mw = this.minWidth;
      if (this.$refs["content"] != null) {
        mw = this.$refs["content"].firstChild.style.minWidth;
      }
      var mh = this.minHeight;
      if (this.$refs["content"] != null) {
        mh = this.$refs["content"].firstChild.style.minHeight;
      }

      return [
        { "min-width": mw },
        { "min-height": mh },
        this.width != undefined ? { width: this.width } : {},
        this.editable && this.hovered ? { overflow: "visible" } : {},
      ];
    },
  },

  mounted() {
    var vm = this;
    this.wComputed = this.$el.style.width;
    this.$el.style.width = this.defaultWidth;

    console.log(this.$slots["content"][0].context.$el);
    window.addEventListener("mousemove", function (e) {
      vm.onMouseMove(e);
    });

    window.addEventListener("mouseup", function (e) {
      vm.onMouseUp(e);
    });

    setTimeout(function () {
      vm.ratio =
        vm.$el.getBoundingClientRect().width /
        vm.$el.getBoundingClientRect().height;
      vm.$emit("onresize", vm.$el.offsetWidth, vm.$el.offsetHeight);
    }, 200);

    var parent = this.getParent("resizable-card");
    if (parent != false) {
      parent.$on("onresize", (w, h) => {
        this.$emit("onresize", vm.$el.offsetWidth, vm.$el.offsetHeight);
      });
    }
  },
  methods: {
    getParent(name) {
      let p = this.$parent;
      while (typeof p !== "undefined") {
        if (p.$options.name == name) {
          return p;
        } else {
          p = p.$parent;
        }
      }
      return false;
    },
    onMouseLeave: function (e) {},
    onMouseEnter: function (e) {},
    onMouseDown: function (e) {
      if (this.resizable == false) return;

      this.$emit("mousedown", e);

      if (this.moveBottom || this.moveRight || this.moveLeft || this.moveTop)
        this.resizeMode = true;

      if (this.resizeMode) this.drag = true;
    },
    onMouseUp: function (e) {
      if (this.resizable == false) return;

      this.$emit("mouseup", e);
      this.moveBottom = false;
      this.moveLeft = false;
      this.moveRight = false;
      this.moveTop = false;
      this.resizeMode = false;
      this.drag = false;
    },
    getRealWidth: function (el) {
      var style = el.currentStyle || window.getComputedStyle(el);
      //console.log(style);
      var gap = 0;
      gap += parseFloat(style.borderLeftWidth.replace("px", ""));
      gap += parseFloat(style.borderRightWidth.replace("px", ""));
      gap += parseFloat(style.paddingLeft.replace("px", ""));
      gap += parseFloat(style.paddingRight.replace("px", ""));
      console.log(gap);
      return el.getBoundingClientRect().width - gap;
    },
    adjustViewBound: function (w, h, priorityWidth) {
      var newh = h;
      var neww = w;

      if (this.keepRatio && priorityWidth) newh = (w * 1) / this.ratio;
      if (this.keepRatio && !priorityWidth) neww = h * this.ratio;

      if (this.dim == "%" || this.dim == undefined) {
        var percent = (100 * neww) / this.getRealWidth(this.$el.parentElement);
        if (percent > 100) percent = 100;
        this.$el.style.width = percent + "%";
      } else this.$el.style.width = neww + "px";

      if (!this.autoheight) {
        if (!priorityWidth || this.keepRatio) {
          this.$el.style.height = newh + "px";
        }
      }

      this.$emit("onresize", this.$el.offsetWidth, this.$el.offsetHeight);

      for (var i = 0; i < this.$children.length; i++) {}
    },
    onMouseMove: function (e) {
      if (this.resizable == false) return;
      this.$emit("mousemove", e);

      var rY = e.y;
      var rect = this.$el.getBoundingClientRect();

      if (this.drag) {
        var style = this.$el.currentStyle || window.getComputedStyle(this.$el);
        var h = rY - rect.top;
        var w = e.x - rect.left;

        if (!this.moveBottom) h = rect.height;
        if (!this.moveRight) w = rect.width;

        this.adjustViewBound(w, h, !this.moveBottom);
        this.wComputed = this.$el.style.width;
      } else {
        this.moveBottom = false;
        this.moveLeft = false;
        this.moveRight = false;
        this.moveTop = false;

        if (
          e.x - this.gap < rect.left &&
          e.x + this.gap > rect.left &&
          rY - this.gap < rect.top + this.$el.offsetHeight &&
          rY + this.gap > rect.top
        ) {
          //this.moveLeft = true;
        }
        if (
          rY - this.gap < rect.top &&
          rY + this.gap > rect.top &&
          e.x - this.gap < rect.left + this.$el.offsetWidth &&
          e.x + this.gap > rect.left
        ) {
          //this.moveTop = true;
        }

        if (
          e.x + this.gap > rect.left + this.$el.offsetWidth &&
          e.x - this.gap < rect.left + this.$el.offsetWidth &&
          rY - this.gap < rect.top + this.$el.offsetHeight &&
          rY + this.gap > rect.top
        ) {
          this.moveRight = true;
        }

        if (
          rY + this.gap > rect.top + this.$el.offsetHeight &&
          rY - this.gap < rect.top + this.$el.offsetHeight &&
          e.x - this.gap < rect.left + this.$el.offsetWidth &&
          e.x + this.gap > rect.left
        ) {
          this.moveBottom = true;
        }
      }
    },
  },
  watch: {
    dim: function (val) {
      var w = this.$el.getBoundingClientRect().width;
      var h = this.$el.getBoundingClientRect().height;
      this.adjustViewBound(w, h, true);
    },
  },
};
</script>