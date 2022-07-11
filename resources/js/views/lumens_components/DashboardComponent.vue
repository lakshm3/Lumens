<template>
  <resizable-card
    class="component draggable"
    :class="{ dragging: dragging }"
    :dim="dim_wish"
    :default-width="defaultWidth"
    :editable="editable"
    @onresize="onResize"
  >
    <template v-slot:options>
      <v-row no-gutters justify="end" align="end" v-if="editable">
        <v-col md="auto" cols="auto" xl="auto">
          <v-btn
            outlined
            x-small
            :title="dim_wish == 'px' ? 'Size in Pixels' : 'Size in % of width'"
            @click="toggleDim"
          >
            {{ wComputed }}
          </v-btn>
          <v-btn
            outlined
            x-small
            :title="is_auto ? 'Height is automatic' : 'Heiht is a fixed size'"
            @click="setAuto"
          >
            {{ is_auto ? "AUTO" : "FIXED" }}
          </v-btn>

          <v-btn
            x-small
            icon
            :disabled="canMoveUp == false"
            @click="$emit('moveUp')"
            ><v-icon>upload</v-icon></v-btn
          >
          <v-btn
            x-small
            icon
            :disabled="canMoveDown == false"
            @click="$emit('moveDown')"
            ><v-icon>download</v-icon></v-btn
          >
          <v-btn
            icon
            x-small
            title="duplicate this component"
            @click="$emit('duplicate')"
          >
            <v-icon>file_copy</v-icon>
          </v-btn>
          <v-btn
            icon
            x-small
            title="delete this component"
            @click="$emit('delete')"
          >
            <v-icon>delete</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </template>
    <template v-slot:content>
      <div>
        <slot name="header"> </slot>
      </div>

      <slot name="content"> </slot>
    </template>
  </resizable-card>
</template>

<style lang="scss" scoped>
.options {
  position: absolute;
  right: 0px;
  bottom: 100%;
  background: rgba(255, 255, 255, 0.678);
}
</style>

<script>
import ResizableCard from './ResizableCard.vue';
export default {
  components:{ResizableCard},
  data() {
    return {
      dim_wish: "px",
      wComputed: 0,
      dragging: false,
      is_auto: true,
    };
  },
  mounted() {
    var vm = this;

    if (this.dim == undefined) this.dim_wish = "%";
    else this.dim_wish = this.dim;

    var vm = this;
    setTimeout(function () {
      vm.wComputed = vm.$el.style.width;
      vm.is_auto = vm.$el.style.height == "auto" || vm.$el.style.height == "";
    }, 200);

    this.el = this.$el;
  },
  methods: {
    onResize: function (w, h) {
      this.$emit("onresize", w, h);
      this.wComputed = this.$el.style.width;
      this.is_auto =
        this.$el.style.height == "auto" || this.$el.style.height == "";
    },
    setAuto: function () {
      this.$el.style.height = "auto";
      this.is_auto =
        this.$el.style.height == "auto" || this.$el.style.height == "";
    },
    toggleDim: function () {
      if (this.dim_wish == "%") this.dim_wish = "px";
      else this.dim_wish = "%";
    },
  },
  props: ["editable", "dim", "defaultWidth","canMoveUp","canMoveDown"],
};
</script>