

import helpFR from "./data/HelpPageFr.json";
import helpEn from "./data/HelpPageEn.json";

export default {
    data() {
        return {
            helpVisible: false,
            helpTop: 0,
            helpLeft: 0,
            help: {}
        };
    },
    methods: {
        hideHelp: function () {
            this.helpVisible = false;
        },
        hasData: function () {
            if (this.engine != undefined && this.engine.measure != undefined) {
                return true;
            }
            return false;
        },
        showHelp: function (key, event) {

            var help = helpEn;
            var helpData;
            if (window.globalData.currentLocale.toLowerCase() == 'fr') help = helpFR;

            if (help[key] != undefined && helpData == undefined) helpData = help[key];
            if (helpFR[key] != undefined && helpData == undefined) helpData = helpFR[key];
            if (helpEn[key] != undefined && helpData == undefined) helpData = helpEn[key];


            this.help = helpData;
            this.helpVisible = true;
            console.log("show help of = " + key);
            this.helpTop = event.currentTarget.getBoundingClientRect().bottom;
            this.helpLeft = event.currentTarget.getBoundingClientRect().left;
        },
        getIntMeasure: function (key, key2) {
            return parseInt(this.getMeasure(key, key2, 0));
        },
        getFloatMeasure: function (key, key2, nbDecimal, factor) {
            var value = parseFloat(this.getMeasure(key, key2, 0));
            value = value * (factor!=undefined ? parseFloat(factor) : 1)
            
            return value.toFixed(nbDecimal);
        },
        getMeasure: function (key, key2, def) {
            if (this.hasData() && this.engine.measure[key] != undefined) {
                var value = this.engine.measure[key][key2];
                if (key2 == "valueInPercent") {
                    return 100 - parseInt(value);
                }
                return value;
            }
            if (def == undefined) def = 0;
            return def;
        },
        hasMeasure: function (key, key2, def) {
            return this.getMeasure(key, key2, "no_data") != "no_data";
        },
        getComponentByTitle: function (title) {
            if (title == "Engine Diagnostic") return "EngineDiagnostic";
            if (title == "Engine Speed & Consumption")
                return "EngineSpeedConsumption";
            if (title == "Cylinder Max Pressure") return "CylinderMaxPressure";
            if (title == "Shaft Diagnostic") return "ShaftDiagnostic";
            if (title == "Shaft Diagnostic 2")
                return "ShaftDiagnosticComplement";
            if (title == "Oil Condition") return "OilCondition";
            if (title == "EDS Indicator") return "EdsIndicator";
            if (title == "Trends Analysis") return "TrendsAnalysis";
            if (title == "Specific Fuel Consumption")
                return "SpecificFuelConsumption";
            if (title == "Localisation Map") return "LocalisationMap";
            if (title == "Turbine") return "Turbine";
            if (title == "Torque") return "Torque";

            return title.replace(" ", "");
        },

    }
};
