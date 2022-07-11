<template>
    <dashboard-component
        class="card eds_indicator"
        :editable="editable"
        v-on="$listeners"
    >
        <template v-slot:content>
            <div class="card-title">EDS Indicators</div>

            <eds-indicators-setup
                :defaultExtras="extras"
                @close="onExtrasEdited"
                v-model="editIndicatorsView"
            ></eds-indicators-setup>

            <v-btn v-if="editable" @click="editIndicatorsView = true"
                >EDIT EDS</v-btn
            >

            <div
                style="text-align:center; margin-bottom:30px;margin-top:15px"
                v-if="extras"
            >
                <div
                    class="label_with_text"
                    v-for="(indicator, i) in extras.meanIndicators"
                    :key="'mean-indicator-' + i"
                >
                    <span style="margin-right:10px">{{
                        $t("indicators." + indicator)
                    }}</span>
                    <labelmeter
                        :value="getMeasure(indicator, 'valueInPercent')"
                        unit="%"
                        style="min-width:70px"
                        :thresholds_danger="thresholds_danger"
                        :thresholds_warn="thresholds_warn"
                        :nodata="!hasData()"
                    ></labelmeter>
                </div>
            </div>

            <div
                style="vertical-align:top; text-align:center; margin-top:20px"
                v-if="extras"
            >
                <table style="width: 100%;">
                    <tr class="title">
                        <th style="font-weight:bold">Cylinder indicators</th>
                        <td></td>
                        <td
                            v-for="(n, i) in getCylindersOrder()"
                            :key="'rand-' + i"
                        >
                            <span v-text="n" style="font-weight:bold"></span>
                        </td>
                    </tr>
                    <template v-for="m in extras.detailIndicators">
                        <tr :key="'indicator-' + m">
                            <th
                                v-text="$t('indicators.' + m)"
                                @mouseover="showHelp(m, $event)"
                                @mouseleave="hideHelp()"
                            ></th>
                            <td style="vertical-align: middle;">
                                <labelmeter
                                    :value="
                                        hasMeasure(m)
                                            ? getMeasure(m, 'valueInPercent')
                                            : 0
                                    "
                                    unit="%"
                                    :nodata="!hasMeasure(m)"
                                    style="min-width:70px"
                                    :thresholds_danger="thresholds_danger"
                                    :thresholds_warn="thresholds_warn"
                                ></labelmeter>
                            </td>
                            <td
                                v-for="(n, i) in getCylindersMeasures(m)"
                                :key="'percylinder-' + m + '-' + i"
                            >
                                <circularmeter
                                    max="100"
                                    :disabled="!hasMeasure(m)"
                                    :value="100 - n"
                                    :thresholds_danger="thresholds_danger"
                                    :thresholds_warn="thresholds_warn"
                                ></circularmeter>
                            </td>
                        </tr>
                    </template>
                </table>
            </div>

            <div v-else style>No data per cylinder, for selected range</div>

            <help-poppup
                v-if="helpVisible"
                :help="help"
                :top="helpTop"
                :left="helpLeft"
            ></help-poppup>
        </template>
    </dashboard-component>
</template>
<style lang="scss" scoped>
.title {
    color: white;
    font-size: 1.2em;
    th,
    td {
        padding-bottom: 20px;
    }
}

th,
td {
    vertical-align: middle;
}

.label_with_text {
    display: inline-block;
    margin: auto 12px;
    padding: 7px 0px;
}

tr:nth-child(even) {
    background: #262626;
}
tr:not(.title) {
    th {
        padding-left: 10px;
    }
}
</style>
<script>
import DashboardComponentUtility from "../DashboardComponentUtility.js";
import EdsIndicatorsSetup from "./EdsIndicatorsSetup.vue";
export default {
    props: ["engine", "editable", "extras"],
    mixins: [DashboardComponentUtility],
    data() {
        return {
            editIndicatorsView: false,

            thresholds_danger: [0, 35],
            thresholds_warn: [35, 65]
        };
    },
    components: { EdsIndicatorsSetup },
    methods: {
        onExtrasEdited(extras) {
            console.log("extraEdited");
            this.editIndicatorsView = false;
            this.$emit("extras-changed", extras);
        },

        getCylindersMeasures: function(key) {
            if (this.hasMeasure(key)){
                return this.engine.measure[key]["cylinderValues"];
            }

            var l = this.getCylindersOrder().length;
            if (l == 0) l = 8;
            var arr = new Array(l);
            for (var i = 0; i < arr.length; i++) arr[i] = 0;
            return arr;
        },
        hasMeasure: function(key) {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure[key] != undefined
            ) {
                return true;
            }
            return false;
        },
        getCylindersOrder: function() {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure["Engine"] != undefined
            )
                return this.engine.measure["Engine"]["FiringOrder"];
            return [];
        },
        getMeasure: function(key, key2) {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure[key] != undefined
            ) {
                var value = parseInt(this.engine.measure[key][key2]);
                if (key2 == "valueInPercent") return 100 - value;
                return value;
            }
            return 0;
        },
        hasData: function() {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure.Status >= 0
            ) {
                return true;
            }
            return false;
        }
    }
};
</script>
