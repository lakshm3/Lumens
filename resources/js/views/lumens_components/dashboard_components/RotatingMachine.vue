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
      <div class="card-title">Rotating Machine (Bearing)</div>
      <div style="text-align: center" class="main_meters">
        <div>
          <speedometer
            :value="getMeasure('BearingGlobal', 'valueInPercent')"
            minWidth="200px"
            unit="%"
            title="Mechanical Health "
            :color="
              hasMeasure('BearingGlobal', 'valueInPercent') ? '' : '#555555'
            "
          >
            <template v-slot:extra>
              <div
                class="status_health green"
                v-if="getMeasure('BearingGlobal', 'valueInPercent') > 65"
              >
                OK
              </div>
              <div
                class="status_health orange"
                v-else-if="getMeasure('BearingGlobal', 'valueInPercent') > 35"
              >
                Need Attention
              </div>
              <div class="status_health red" v-else>Recommended Action</div>
            </template>
          </speedometer>
          <speedometer
            :value="getFloatMeasure('GlobalLevel', 'value', 2)"
            minWidth="200px"
            max="100"
            unit="mm/s"
            :inverted-color="true"
            title="Level (RMS)"
            :color="hasMeasure('GlobalLevel', 'value') ? '' : '#555555'"
          ></speedometer>
          <speedometer
            :value="getMeasure('GlobalKurto', 'valueInPercent')"
            minWidth="200px"
            unit="%"
            title="Shock index"
            :color="
              hasMeasure('GlobalKurto', 'valueInPercent') ? '' : '#555555'
            "
          ></speedometer>
        </div>
        <div style="clear: both"></div>
        <div>
          <speedometer
            :value="getMeasure('GlobalMixed', 'valueInPercent')"
            minWidth="150px"
            unit="%"
            title="Global"
            :color="
              hasMeasure('GlobalMixed', 'valueInPercent') ? '' : '#555555'
            "
            definition="Unbalance / Instability / Misalignment / Structural defects (modal, loosness)"
          ></speedometer>

          <speedometer
            :value="getMeasure('2KMixed', 'valueInPercent')"
            minWidth="150px"
            unit="%"
            title="Shaft/Clearance"
            :color="hasMeasure('2KMixed', 'valueInPercent') ? '' : '#555555'"
            definition="Assembly and guide clearance<br/>Global surface defect"
          ></speedometer>
          <speedometer
            :value="getMeasure('4KMixed', 'valueInPercent')"
            minWidth="150px"
            unit="%"
            title="Bearings"
            :color="hasMeasure('4KMixed', 'valueInPercent') ? '' : '#555555'"
            definition="Local surface and assembly faults<br/>Housing wear / Cavitation"
          ></speedometer>
          <speedometer
            :value="getMeasure('8KMixed', 'valueInPercent')"
            minWidth="150px"
            unit="%"
            title="Friction"
            :color="hasMeasure('8KMixed', 'valueInPercent') ? '' : '#555555'"
            definition="Lubrification defect<br/>Contact strain / Fretting<br/>Cavitation"
          ></speedometer>
        </div>
      </div>
    </template>
  </dashboard-component>
</template>
<style lang="scss" scoped>
.status_health {
  padding: 10px;
  margin-top: 20px;
}
.status_health.green {
  background-color: #2d8431;
}
.status_health.orange {
  background-color: #f37021;
}
.status_health.red {
  background-color: #b9261c;
}
</style>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";

export default {
  props: ["engine", "editable"],
  mixins: [DashboardComponentUtility],
};
</script>