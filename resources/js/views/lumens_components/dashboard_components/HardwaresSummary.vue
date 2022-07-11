<template>
  <dashboard-component
    class="card"
    id="test"
    width="calc(100% - 20px)"
    minWidth="188px"
    :editable="editable"
    v-on="$listeners"
  >
    <template v-slot:content>
      <div class="card-title">Equipment Summary</div>
      <div class="table-container">
        <table>
          <th-orderable-row :columns="[{title:'Hardware'},{title:'Type'}, {title:'Last value'}]"></th-orderable-row>

          <tr v-for="(h,i) in hardwares" :key="'h-'+i" class="model-list-item not-hoverable">
            <td>Hardware {{i+1}}</td>
            <td v-text="h.type"></td>
            <td>{{ h.date?''+ h.date:'No measure yet'}}</td>
          </tr>
        </table>
        <div v-if="!hardwares || hardwares.length==0">No hardwares connected yet</div>
      </div>
    </template>
  </dashboard-component>
</template>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";

export default {
  props: ["hardwares", "editable"],

  mixins: [DashboardComponentUtility],
  mounted() {},
  methods: {},
  watch: {
    "engine.measure": function (newVal) {
      //this.createSpiderChart();
    },
  },
};
</script>