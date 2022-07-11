<template>
  <tr class="model-list-item">
    <th
      v-for="(c,i) in columns"
      :key="'c-' +i"
      :class="{'selected_order': orderBy==c.key, 'orderable':c.orderable}"
      @click="changeOrder(c.key)"
    >{{c.title}}</th>
  </tr>
</template>
<script>
export default {
  props: ["columns", "orderBy", "orderWay"],

  methods: {
    changeOrder: function (key) {
      this.$emit("changeOrder", key);
    },
  },
};
</script>