<template>
  <div class="notification" :class="notification.type">
    <div v-text="notification.title" class="title"></div>
    <div v-if="notification.description" v-text="notification.description" class="description"></div>
  </div>
</template>
<style lang="scss" scoped>
.notification {
  padding: 15px;
  box-sizing: border-box;
  border-radius: 3px;
  background-color: #303030;
  box-shadow: 0px 1px 3px #0000008c;
  margin-top: 20px;
  transition: 1s;

  .title {
    margin-bottom: 5px;
    font-size: 1em;
  }
  .description {
    font-size: 0.9em;
  }
}

.notification.hide {
  display: none;
}
.notification.error {
  background-color: #691313;
}
.notification.success {
  background-color: #2b712f;
}
.notification.warning {
  background-color: #844213;
}
</style>
<script>
export default {
  props: ["notification"],
  data() {
    return {
      hide: false,
    };
  },

  mounted() {
    var vm = this;
    setTimeout(function () {
      vm.hide = true;
      vm.$emit("remove");
    }, 10000);
  },
};
</script>