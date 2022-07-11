<template>
    <dashboard-component
        class="card"
        minWidth="600px"
        width="80%"
        :editable="editable"
        v-on="$listeners"
    >
        <template v-slot:content>
            <div class="card-title">Cylinder Max Pressure</div>
            <bar-chart
                ref="chart"
                style="height:250px"
                :chart-data="chartDataCollection"
                :options="chartOptions"
            ></bar-chart>
        </template>
    </dashboard-component>
</template>

<script>

//import measure from "./../../tests/measure.json"

export default {
    props: [ "editable","engine"],
    data() {
        return {
            chartDataCollection: {},
            chartOptions: {}
        };
    },
    mounted() {
        //this.engine.measure = measure;
        this.refreshChart();
    },
    methods: {
        refreshChart: function(durationAnimation) {
            
            this.chartDataCollection = {
                labels: this.getFiringOrders(),
                datasets: [
                    /*
                    {
                        label: "Pressure Max",
                        data: this.getCylindersPressures(0,"Pressure_max"),
                        yAxisID: "A",
                        borderColor: "rgb(123,139,203)",
                        pointBackgroundColor: "rgb(123,139,203)",
                        backgroundColor: "rgba(123,139,203,0.49)",
                        pointRadius: 2,
                        lineWidth: 1,
                        showLine: true,
                        borderWidth: 1,
                        fill: false
                    },*/
                   
                    {
                        label: "Average Pressure",
                        data: this.getCylindersPressures(1,"Pressure"),
                        yAxisID: "A",
                        borderColor: "rgb(203,203,139)",
                        pointBackgroundColor: "rgb(203,203,139)",
                        backgroundColor: "rgb(203,203,139,0.49)",
                        pointRadius: 2,
                        lineWidth: 1,
                        showLine: true,
                        borderWidth: 1,
                        fill: false
                    }
                ]
            };

            var i = 0;

            this.chartOptions = {
                animation: {
                    duration: durationAnimation
                },
                maintainAspectRatio: false,
                responsive: true,
                lineTension: 1,
                onClick: this.handleClick,
                legend: {
                    position: "bottom",
                    labels: {
                        fontColor: "#FFFFFF",
                        usePointStyle: true
                    }
                },
                scales: {
                    xAxes: [
                        {
                            gridLines: {
                                color: "#FFFFFF22"
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "Cylinder",
                                fontColor: "#FFFFFF"
                            }
                        }
                    ],
                    yAxes: [
                        {
                            id: "A",
                            gridLines: {
                                color: "#FFFFFF22"
                            },
                            position: "left",
                            ticks: {
                                fontColor: "white",
                                beginAtZero: false,
                                min: 0
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "bar",
                                fontColor: "#FFFFFF"
                            }
                        }
                    ]
                }
            };

            this.$refs.chart.renderChart(
                this.chartDataCollection,
                this.chartOptions
            );
        },

        getFiringOrders: function() {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure["Engine"] != undefined
            ) {
                return this.engine.measure["Engine"]["FiringOrder"];
            }
            return [];
        },
        getCylindersPressures: function(first, key) {
            if (
                this.engine != undefined &&
                this.engine.measure != undefined &&
                this.engine.measure[key] != undefined
            )
                return this.engine.measure[key]["cylinderValues"];
            return [];
        },
        getMeanPressure: function() {
            var s = 0;
            var cylinders = this.getCylindersPressures();
            for (var i = 0; i < cylinders.length; i++) {
                s += cylinders[i];
            }

            return parseInt(s / cylinders.length);
        },
    },
    watch:{
        "engine.measure":function(newValue){
            this.refreshChart();
        }
    }
};
</script>
