<template>
  <div style="height: 100%">
    <v-row>
      <v-col>
        <h3>
          {{ value.title }}
        </h3>
      </v-col>
      <v-spacer></v-spacer>
      <v-col md="auto" cols="auto" xl="auto" sm="auto">
        <div
          style="
            background: #3a3a6b;
            height: 35px;
            font-size: 0.9em;
            padding-left: 12px;
            padding-right: 12px;
          "
          class="rounded-lg"
          v-if="timeSelected != -1 && !inLive"
        >
          <small>Selected Point</small><br />
          <b style="margin-top: -5px; display: block">
            {{ timeSelected | formatDate }}</b
          >
        </div>
      </v-col>
      <v-col md="auto" cols="auto" xl="auto" sm="auto">
        <toggle
          v-model="inLive"
          class="rounded-lg"
          height="35px"
          style="font-weight: 500"
          @input="updateMode()"
          colorOn="#128134"
          colorOff="#3a3a6b"
          :textOn="'● ' + $t('components.trends.in_live')"
          :textOff="$t('components.trends.history')"
        ></toggle>
      </v-col>
    </v-row>

    <div style="vertical-align: top; text-align: center; height: 100%">
      <div style="float: left; width: 200px">
        <labelmeter
          v-if="value.show_rpm_graph"
          :nodata="!hasMeasure(value.rpm_indicator)"
          style="display: block"
          :value="getMeasure(value.rpm_indicator, 0)"
          :label="$t('indicators.speed') + ' (rpm)'"
          :thresholds_danger="[152, 999]"
          :thresholds_warn="[0, 145]"
        ></labelmeter>

        <div>
          <h3 style="text-align: center; margin-top: 40px; margin-bottom: 20px">
            {{ $t("components.trends.indicators") }}
          </h3>

          <template v-if="value">
            <template v-for="(indicator, k) in value.lateralIndicators">
              <labelmeter
                :key="'trendind-' + k"
                v-if="k < 5"
                class="selectable_indicator"
                :label="$t('indicators.' + indicator)"
                :class="{
                  selected: selectedIndicator == indicator,
                }"
                v-on:click.native="showIndicatorGraph(indicator)"
                :value="getCurrentIndicatorValue(indicator, 0)"
                :thresholds_danger="[0, 35]"
                :thresholds_warn="[35, 65]"
                :color="findIndicatorForKey(indicator).unit ? '#7eb338' : ''"
                :nodata="getCurrentIndicatorValue(indicator) == undefined"
              ></labelmeter>
              <v-select
                v-if="k >= 5 && k < 6"
                :key="'trendind-' + k"
                :items="value.lateralIndicators"
              >
                <template v-slot:selection="{ item, index }">
                  <labelmeter
                    class="selectable_indicator"
                    :label="$t('indicators.' + item)"
                    :class="{
                      selected: selectedIndicator == item,
                    }"
                    v-on:click.native="showIndicatorGraph(item)"
                    :value="getCurrentIndicatorValue(item, 0)"
                    :thresholds_danger="[0, 35]"
                    :thresholds_warn="[35, 65]"
                    :color="findIndicatorForKey(item).unit ? '#7eb338' : ''"
                    :nodata="getCurrentIndicatorValue(item) == undefined"
                  ></labelmeter>
                </template>
              </v-select>
            </template>
          </template>

          <select name="xaxe" @change="changeXAxe()" v-model="xaxe">
            <option value="DateAndTime">
              {{ $t("components.trends.time") }}
            </option>
            <option value="ChannelSpeed">RPM</option>
          </select>
        </div>
      </div>
      <div
        style="display: inline-block; width: calc(100% - 200px); height: 100%"
      >
        <line-chart
          v-if="value.show_rpm_graph"
          ref="firstChart"
          style="height: 100px; max-height: 50vh"
          :chart-data="firstChartDataCollection"
          :options="firstOptions"
          @selection-zoom="changeZoomFilter"
        ></line-chart>
        <line-chart
          ref="sndChart"
          @selection-zoom="changeZoomFilter"
          :style="{
            maxHeight: '65vh',
            height: value.show_rpm_graph
              ? 'calc(95% - 250px )'
              : 'calc(95% - 150px)',
          }"
          :chart-data="sndChartDataCollection"
          :options="sndOptions"
        ></line-chart>

        <div
          style="float: right; font-size: 0.9em; opacity: 0.4; font-weight: 300"
        >
          <loader
            :size="20"
            style="display: inline; margin-right: 30px"
            v-if="loadingData"
          ></loader>

          <span v-if="loadingData" style="line-height: 20px">{{
            $t("components.trends.loading_data")
          }}</span>
          <span v-else style="line-height: 20px">
            {{
              $t("components.trends.points_retrieved", {
                nb: Object.keys(measures).length,
                time: lastTimeToRetrieve.toFixed(3),
              })
            }}
          </span>
        </div>

        <v-row class="mt-6" align="center">
          <v-col v-if="!inLive">
            <v-datetime-picker
              outlined
              dateFormat="yyyy-MM-dd"
              timeFormat="HH:mm"
              :label="$t('components.trends.from')"
              v-model="fromDate"
              :textFieldProps="{ outlined: true, dense: true }"
            >
            </v-datetime-picker>
          </v-col>
          <v-col v-if="!inLive">
            <v-datetime-picker
              outlined
              dateFormat="yyyy-MM-dd"
              timeFormat="HH:mm"
              :label="$t('components.trends.to')"
              v-model="toDate"
              :textFieldProps="{ outlined: true, dense: true }"
            >
            </v-datetime-picker>
          </v-col>
          <v-col md="auto" sm="auto" cols="auto" xl="auto" lg="auto">
            <v-select
              outlined
              :label="$t('components.trends.granularity')"
              v-model="granularity"
              style="max-width: 150px"
              @change="getDatas(true)"
              dense
              :items="granularities"
              item-text="title"
              item-value="key"
            >
            </v-select>
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
input,
select {
  background-color: transparent;
  outline-color: white;

  border: 1px solid white;
  padding: 8px 9px;
  font-family: Raleway, sans-serif;
  color: white;
  border-radius: 3px;

  option {
    background-color: #202020;
  }
}
</style>
<script>
export default {
  props: ["value", "editable", "engine"],

  data() {
    return {
      editIndicatorsView: false,
      firstChartDataCollection: {},
      sndChartDataCollection: {},
      firstOptions: {},
      sndOptions: {},
      inLive: this.$route.query.fromDate == undefined,
      granularity:
        this.$route.query.granularity != undefined
          ? this.$route.query.granularity
          : "1min",
      fromDate: this.$route.query.fromDate
        ? new Date(this.$route.query.fromDate)
        : null,
      toDate: this.$route.query.toDate
        ? new Date(this.$route.query.toDate)
        : null,
      measures: {},
      loadingData: false,
      selectedIndicator: "",
      xaxe: "DateAndTime",
      firstDisplay: true,
      lastTimeToRetrieve: 0,
      getDatasRequestToken: null,
      intervalLive: null,
      timeSelected: -1,
      granularities: [
        {
          key: "1min",
          title: "1 minute",
        },
        {
          key: "10min",
          title: "10 minutes",
        },
        {
          key: "1hour",
          title: "1 hour",
        },
        {
          key: "1day",
          title: "1 day",
        },
        {
          key: "1week",
          title: "1 week",
        },
      ],
    };
  },

  mounted() {
    if (
      this.value.lateralIndicators != null &&
      this.value.lateralIndicators.length > 0
    )
      this.selectedIndicator = this.value.lateralIndicators[0];

    this.refreshChart(500);
    if (this.engine) {
      this.getDatas();
      this.updateMode();
    }
  },
  methods: {
    onResize: function (w, h) {
      this.$emit("onresize", w, h);
      this.is_auto =
        this.$el.style.height == "auto" || this.$el.style.height == "";
    },

    adjustGranularity: function () {
      var oldG = this.granularity;
      var d1 = this.fromDate.getTime();
      var d2 = this.toDate.getTime();

      var duration = d2 / 1000 - d1 / 1000;

      console.log(
        "fromDate = " +
          this.moment(this.fromDate.toString()).format("YYYY-MM-DD HH:mm:ss")
      );
      console.log(
        "toDate = " +
          this.moment(this.toDate.toString()).format("YYYY-MM-DD HH:mm:ss")
      );

      if (duration < 3600 + 1200) this.granularity = "1min";
      else if (duration < 3600 * 10) this.granularity = "10min";
      else if (duration < 3600 * 24 * 10) this.granularity = "1hour";
      else if (duration < 3600 * 24 * 7 * 10) this.granularity = "1day";
      else this.granularity = "1week";

      this.addParamsToLocation({
        fromDate: this.moment(this.fromDate.toString()).format(
          "YYYY-MM-DD HH:mm:ss"
        ),
        toDate: this.moment(this.toDate.toString()).format(
          "YYYY-MM-DD HH:mm:ss"
        ),
        granularity: this.granularity,
      });

      this.getDatas(true);
    },
    changeXAxe: function () {
      console.log(this.xaxe);
      this.refreshChart(500);
    },
    revertValues: function (array) {
      for (var i = 0; i < array.length; i++) {
        array[i].y = 100 - array[i].y;
      }
    },
    refreshChart: function (durationAnimation) {
      this.firstChartDataCollection = {
        labels: this.getXDatasArr(),
        datasets: [
          {
            label: "RPM",
            data: this.getDatasArr("ChannelSpeed"),
            yAxisID: "A",
            borderColor: "rgb(123,203,139)",
            pointBackgroundColor: "rgb(123,203,139)",
            backgroundColor: "rgba(123,203,139,0.49)",
            pointRadius: 2,
            lineWidth: 1,
            showLine: true,
            borderWidth: 1,
            fill: false,
          },
        ],
      };

      this.colors = [
        "rgba(203,203,90,0.49)",
        "rgba(123,203,139,0.49)",
        "rgba(0,164,220,0.5)",
        "rgba(211,57,224,0.5)",
        "rgba(255,255,255,0.5)",
      ];

      var datasets = [];
      var i = 0;
      var ind;

      for (i = 0; i < this.value.graphIndicators.length; i++) {
        if (i >= 4) break;
        ind = this.findIndicatorForKey(this.value.graphIndicators[i]);
        datasets.push({
          label: this.$t("indicators." + this.value.graphIndicators[i]),
          data: this.getDatasArr(
            this.value.graphIndicators[i],
            ind.unit ? "value" : "valueInPercent"
          ),
          yAxisID: "A",
          backgroundColor: this.colors[i],
          pointBackgroundColor: this.colors[i],
          borderColor: this.colors[i],
          fill: false,
          pointRadius: 2,
          lineWidth: 1,
          showLine: this.xaxe == "DateAndTime",
          borderWidth: 1,
        });
      }

      if (this.value.comparative_mode) {
        ind = this.findIndicatorForKey(this.selectedIndicator);

        datasets.push({
          label: this.$t("indicators." + this.selectedIndicator),
          data: this.getDatasArr(
            this.selectedIndicator,
            ind.unit ? "value" : "valueInPercent"
          ),
          yAxisID: "A",
          backgroundColor: this.colors[i],
          pointBackgroundColor: this.colors[i],
          borderColor: this.colors[i],
          fill: false,
          pointRadius: 2,
          lineWidth: 1,
          showLine: this.xaxe == "DateAndTime",
          borderWidth: 1,
        });
      }

      //console.log("Indicator for unit =>" + ind);
      //console.log(ind);

      this.sndChartDataCollection = {
        labels: this.getXDatasArr(),
        datasets: datasets,
      };

      this.firstOptions = {
        animation: {
          duration: durationAnimation,
        },
        maintainAspectRatio: false,
        responsive: true,
        lineTension: 1,
        onClick: this.handleClick,
        legend: {
          position: "bottom",
          labels: {
            fontColor: "#FFFFFF",
            usePointStyle: true,
          },
        },
        scales: {
          xAxes: [
            {
              gridLines: {
                color: "#FFFFFF22",
              },
              scaleLabel: {
                display: true,
                labelString: "Time",
                fontColor: "#FFFFFF",
              },
              ticks: {
                fontColor: "white",
                autoSkip: true,
                maxTicksLimit: 10,
                display: false,
              },
              type: "time",
              distribution: "linear",
              min: 0,
              max: 1573853353000,
              time: {
                minUnit: "minute",
                displayFormats: {
                  millisecond: "HH:mm:ss.SSS",
                  second: "HH:mm:ss",
                  minute: "HH:mm",
                  hour: "MMM D, HH:mm",
                  day: "MMM D HH:mm",
                  month: "YYYY MMM",
                  year: "YYYY",
                },
                parser: function (utcMoment) {
                  return utcMoment.utcOffset("+0100");
                },
              },
            },
          ],
          yAxes: [
            {
              id: "A",
              gridLines: {
                color: "#FFFFFF22",
              },
              position: "left",
              ticks: {
                fontColor: "white",
                beginAtZero: true,
                min: 0,
              },
              scaleLabel: {
                display: true,
                labelString: "RPM",
                fontColor: "#FFFFFF",
              },
            },
          ],
        },
      };
      this.sndOptions = {
        animation: {
          duration: durationAnimation,
        },
        maintainAspectRatio: false,
        responsive: true,
        lineTension: 1,
        onClick: this.handleClick,
        legend: {
          position: "bottom",
          labels: {
            fontColor: "#FFFFFF",
            usePointStyle: true,
          },
        },

        height: 20000,
        scales: {
          xAxes: [
            {
              gridLines: {
                color: "#FFFFFF22",
              },
              scaleLabel: {
                display: true,
                labelString: "Time",
                fontColor: "#FFFFFF",
              },
              ticks: {
                fontColor: "white",
                autoSkip: true,
                autoSkipPadding: 102,
                maxRotation: 20,
                includeBounds: true,
              },
              type: "time",
              distribution: "linear",
              min: 0,
              max: 1573853353000,
              time: {
                minUnit: "minute",
                displayFormats: {
                  millisecond: "HH:mm:ss.SSS",
                  second: "HH:mm:ss",
                  minute: "HH:mm",
                  hour: "MMM D, HH:mm",
                  day: "MMM D HH:mm",
                  month: "YYYY MMM",
                  year: "YYYY",
                },
              },
            },
          ],
          yAxes: [
            {
              id: "A",
              type: "linear",
              position: "left",
              gridLines: {
                color: "#FFFFFF22",
              },
              ticks: {
                fontColor: "white",
                beginAtZero: !ind.unit ? true : undefined,
                steps: !ind.unit ? 10 : undefined,
                stepValue: !ind.unit ? 10 : undefined,
                max: !ind.unit ? 100 : undefined,
                min: !ind.unit ? 0 : undefined,
              },
              scaleLabel: {
                display: true,
                labelString:
                  this.$t("components.trends.health") +
                  " (" +
                  (ind.unit ? ind.unit : "%") +
                  ")",
                fontColor: "#FFFFFF",
              },
            },
          ],
        },
      };

      if (this.xaxe == "DateAndTime") {
        this.$set(this.sndOptions.scales.xAxes[0], "scaleLabel", "Time");
        this.$set(this.sndOptions.scales.xAxes[0], "type", "time");
        this.$set(this.sndOptions.scales.xAxes[0], "distribution", "linear");
        this.$set(this.sndOptions.scales.xAxes[0], "time", {
          minUnit: "minute",
          displayFormats: {
            millisecond: "HH:mm:ss.SSS",
            second: "HH:mm:ss",
            minute: "HH:mm",
            hour: "MMM D, HH:mm",
            day: "MMM D HH:mm",
            month: "YYYY MMM",
            year: "YYYY",
          },
        });
        this.$set(this.firstOptions.scales.xAxes[0], "scaleLabel", "Time");
        this.$set(this.firstOptions.scales.xAxes[0], "type", "time");
        this.$set(this.firstOptions.scales.xAxes[0], "distribution", "linear");
        this.$set(this.firstOptions.scales.xAxes[0], "time", {
          minUnit: "minute",
          displayFormats: {
            millisecond: "HH:mm:ss.SSS",
            second: "HH:mm:ss",
            minute: "HH:mm",
            hour: "MMM D, HH:mm",
            day: "MMM D HH:mm",
            month: "YYYY MMM",
            year: "YYYY",
          },
        });
      } else {
        this.$set(this.sndOptions.scales.xAxes[0], "scaleLabel", "Rpm");
        this.$set(this.sndOptions.scales.xAxes[0], "type", "linear");
        this.$delete(this.sndOptions.scales.xAxes[0], "distribution");
        this.$delete(this.sndOptions.scales.xAxes[0], "time");

        this.$set(this.firstOptions.scales.xAxes[0], "scaleLabel", "Rpm");
        this.$set(this.firstOptions.scales.xAxes[0], "type", "linear");
        this.$delete(this.firstOptions.scales.xAxes[0], "distribution");
        this.$delete(this.firstOptions.scales.xAxes[0], "time");
      }

      this.$refs.firstChart.renderChart(
        this.firstChartDataCollection,
        this.firstOptions
      );
      this.$refs.sndChart.renderChart(
        this.sndChartDataCollection,
        this.sndOptions
      );
      //this.$refs.sndChart.refreshChart();
    },

    getDatas: function (hardReload) {
      hardReload = true;

      if (this.getDatasRequestToken != null) this.getDatasRequestToken.cancel();
      this.getDatasRequestToken = axios.CancelToken.source();

      var query = "";
      if (this.inLive) {
        query = "live&granularity=" + this.granularity;
        if (Object.keys(this.measures).length > 0 && !hardReload)
          query += "&lastPoint=" + this.getMostRecentDate();
      } else {
        query =
          "fromDate=" +
          this.moment(this.fromDate.toString()).format("YYYY-MM-DD HH:mm:ss") +
          "&toDate=" +
          this.moment(this.toDate.toString()).format("YYYY-MM-DD HH:mm:ss") +
          "&granularity=" +
          this.granularity;
      }

      this.loadingData = true;
      if (this.engine) {
        axios
          .get("/api/engines/" + this.getEngineId() + "/datas?" + query, {
            cancelToken: this.getDatasRequestToken.token,
          })
          .then((result) => {
            if (!result) return;
            this.loadingData = false;

            if (hardReload) this.$set(this, "measures", {});

            if (this.inLive) {
              this.measures = {
                ...this.measures,
                ...result.data.measures,
              };
              this.fromDate = new Date(result.data.from_date.substring(0, 16));
              this.toDate = new Date(result.data.to_date.substring(0, 16));
            } else {
              this.measures = result.data.measures;
            }
            this.lastTimeToRetrieve = result.data.time_to_retrieve;

            if (this.inLive) {
              this.$emit(
                "measureSelected",
                this.getEngineId(),
                this.getLivePoint(),
                this.getMostRecentDate()
              );
            }

            this.refreshChart(this.firstDisplay ? 500 : 0);
            this.firstDisplay = false;
          })
          .catch((err) => {
            this.loadingData = false;
            console.error(err);
          });
      }
    },

    getEngineId: function () {
      return this.engine && this.engine.id ? this.engine.id : this.engine._id;
    },

    getMostRecentDate: function () {
      var mdate = "";
      for (var date in this.measures) {
        if (date > mdate || mdate == "") mdate = date;
      }
      return mdate;
    },
    getLivePoint: function () {
      return this.measures[this.getMostRecentDate()];
    },

    getDatasArr: function (key, key2) {
      var arr = [];
      for (var ts in this.measures) {
        var val = this.measures[ts][key];
        if (val == undefined) val = 0;
        else if (key2 != undefined) {
          val = val[key2];
        }

        if (val == undefined) val = 0;

        val = parseFloat(val);
        if (key2 == "valueInPercent") val = 100 - val;

        if (this.xaxe == "DateAndTime") {
          var date = new Date();
          date.setTime(this.toUTCTimestampMillis(ts, date));

          var elt = {
            t: date,
            y: val,
          };
        } else {
          var elt = {
            x: this.measures[ts][this.xaxe],
            y: val,
          };
        }

        arr.push(elt);
      }

      // sort on key values
      function valsrt() {
        return function (a, b) {
          if (a.x > b.x) return 1;
          if (a.x < b.x) return -1;
          return 0;
        };
      }

      return arr.sort(valsrt());
    },

    toUTCTimestampMillis(ts, date) {
      return ts * 1000 + date.getTimezoneOffset() * 60 * 1000;
    },
    fromUTCTimestampMillis(ts, date) {
      return (ts - date.getTimezoneOffset() * 60 * 1000) / 1000;
    },
    getXDatasArr: function () {
      var arr = [];
      for (var ts in this.measures) {
        if (this.xaxe == "DateAndTime") {
          var date = new Date();
          date.setTime(this.toUTCTimestampMillis(ts, date));
          arr.push(date);
        } else {
          var val = this.measures[ts][this.xaxe];
          arr.push(val);
        }
      }
      if (arr.length == 0) {
        if (this.xaxe == "DateAndTime") {
          arr.push(this.fromDate);
          arr.push(this.toDate);
        }
      }
      return arr;
    },
    showIndicatorGraph: function (indicator) {
      this.selectedIndicator = indicator;
      this.refreshChart(500);
    },
    updateMode: function () {
      console.log("Mode inlive = " + this.inLive);

      clearInterval(this.intervalLive);

      if (this.inLive) {
        this.timeSelected = -1;
        this.addParamsToLocation({});
        this.measures = [];
        if (!this.firstDisplay) this.getDatas(true);

        this.intervalLive = setInterval(() => {
          if (this.inLive) this.getDatas();
        }, 10000);
      } else {
        this.getDatas();
      }
    },

    getMeasure: function (key, defValue) {
      if (this.engine != undefined && this.engine.measure != undefined)
        return this.engine.measure[key];
      if (defValue == undefined) defValue = "0";
      return defValue;
    },
    hasMeasure: function (key) {
      if (
        this.engine != undefined &&
        this.engine.measure != undefined &&
        this.engine.measure[key] != undefined
      )
        return true;
      return false;
    },

    getMeasureValue: function (key, defValue) {
      if (
        this.engine != undefined &&
        this.engine.measure != undefined &&
        this.engine.measure[key] != undefined
      )
        return this.engine.measure[key]["value"];

      if (defValue == undefined) defValue = "0";
      return defValue;
    },
    changeZoomFilter(fromDateChart, toDateChart) {
      this.fromDate = new Date(fromDateChart);
      this.toDate = new Date(toDateChart);
      this.inLive = false;

      console.log(
        "Change Zoom Filter fromDate: " +
          this.fromDate +
          ", ToDate:" +
          this.toDate
      );
    },
    getMeasureValuePercent: function (key, defValue) {
      if (
        this.engine != undefined &&
        this.engine.measure != undefined &&
        this.engine.measure[key] != undefined
      )
        return this.engine.measure[key]["valueInPercent"];

      if (defValue == undefined) defValue = "0";
      return defValue;
    },
    getCurrentIndicatorValue: function (indicator, defaultValue) {
      if (
        this.engine != undefined &&
        this.engine.measure != undefined &&
        this.engine.measure[indicator] != undefined
      ) {
        var ind = this.findIndicatorForKey(indicator);
        var value = null;
        if (ind.unit) value = this.engine.measure[indicator]["value"];
        else value = 100 - this.engine.measure[indicator]["valueInPercent"];

        return parseFloat(value).toFixed(ind.fixed ? ind.fixed : 0);
      }
      return defaultValue;
    },

    handleClick: function (evt, point) {
      console.log("Handle Click Chart");
      if (point[0] != undefined) {
        var index = point[0]._index;
        var label = point[0]._chart.data.labels[index];
        var selectedMeasure =
          this.measures[
            this.fromUTCTimestampMillis(label.getTime(), new Date())
          ];
        this.timeSelected = label.getTime() / 1000;
        console.log(
          "Click =>  measureSelected:" +
            selectedMeasure +
            ", timeSelected:" +
            this.timeSelected
        );
        this.$emit("measureSelected", this.getEngineId(), selectedMeasure);
        this.inLive = false;
        clearInterval(this.intervalLive);
      }
    },
  },

  beforeDestroy: function () {
    console.log("destroy called = " + this.intervalLive);
    clearInterval(this.intervalLive);
  },
  watch: {
    engine: function (newVal) {
      this.refreshChart(500);
      this.updateMode();
    },
    toDate: function (newVal) {
      if (!this.inLive) this.adjustGranularity();
    },
    fromDate: function (newVal) {
      if (!this.inLive) this.adjustGranularity();
    },
  },
};
</script>
