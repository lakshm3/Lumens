<template>
    <dashboard-component
        class="card"
        minWidth="120px"
        :editable="editable"
        v-on="$listeners"
    >
        <template v-slot:content>
            <div class="card-title">Torque</div>
            <div style="text-align:center;" class="main_meters">
                <speedometer
                    color="#7eb338"
                    max="1"
                    :value="getFloatMeasure('StaticTorsion','value',2)"
                    minWidth="150px"
                    type="0"
                    unit="°"
                    title="Torsion"
                ></speedometer>
                <speedometer
                    max="1000000"
                    color="#7eb338"
                    :value="getIntMeasure('StaticTorque','value')"
                    minWidth="150px"
                    type="0"
                    unit="Nm"
                    title="Torque"
                ></speedometer>
                <speedometer
                    max="1000000"
                    color="#7eb338"
                    :value="getIntMeasure('StaticPower','value')"
                    minWidth="150px"
                    type="0"
                    unit="W"
                    title="Power"
                ></speedometer>
            </div>
        </template>
    </dashboard-component>
</template>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";

export default {
    props: ["engine", "editable"],
    mixins: [DashboardComponentUtility]
};
</script>
