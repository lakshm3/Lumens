import Vue from "vue";
import VueRouter from "vue-router";
import i18n from './translations'
import storage from './storage'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader

window.globalData = storage;
i18n.locale = storage.currentLocale;

Vue.use(VueRouter);

import App from "./views/App";
import Home from "./views/Home";
import Loader from "./views/ui/Loader";
import Notification from "./views/ui/Notification";
import Toggle from "./views/ui/Toggle";
import ThOrderableRow from "./views/ui/ThOrderableRow";
import EditSettings from "./views/EditSettings";
import InterfacesList from "./views/interfaces/InterfacesList";

import LineChart from "./linechart.js";
import BarChart from "./barchart";
import SpiderChart from "./spiderchart.js";
localStorage.setItem("access_token", "dijdkzedz");
window.sha1 = require("js-sha1");
window.html2canvas = require("html2canvas");
window.axios = require("axios");
window.axios.interceptors.request.use(function (config) {
    var accessToken = localStorage.getItem("access_token");
    if (accessToken != undefined) config.headers.Authorization = accessToken;
    return config;
});

Vue.component("line-chart", LineChart);
Vue.component("bar-chart", BarChart);
Vue.component("spider-chart", SpiderChart);
Vue.component("loader", Loader);
Vue.component("notification", Notification);
Vue.component("toggle", Toggle);
Vue.component("th-orderable-row", ThOrderableRow);
Vue.component("interfaces-list", InterfacesList);


import DashboardComponent from "./views/lumens_components/DashboardComponent.vue";
import ResizableCard from "./views/lumens_components/ResizableCard.vue";
import HelpPoppup from "./views/lumens_components/HelpPoppup.vue";
import InterfacesEdit from "./views/lumens_components/InterfacesCreateEdit.vue";
import ComponentSection from "./views/lumens_components/ComponentSection.vue";
import ComponentChart from "./views/lumens_components/ComponentChart.vue";
import QuaterMeter from "./views/lumens_components/meters/QuaterMeter";
import LabelMeter from "./views/lumens_components/meters/LabelMeter";
import CircularMeter from "./views/lumens_components/meters/CircularMeter";
import Speedometer from "./views/lumens_components/meters/Speedometer";
import SpeedometerGraph from "./views/lumens_components/meters/SpeedometerGraph.vue";
import SpeedometerGraphQuarter from "./views/lumens_components/meters/SpeedometerGraphQuarter";
import moment from 'moment'

import DatetimePicker from 'vuetify-datetime-picker'
Vue.prototype.moment = moment
Vue.use(DatetimePicker);
Vue.component("help-poppup", HelpPoppup);
Vue.component("interfaces-edit", InterfacesEdit);
Vue.component("dashboard-component", DashboardComponent);
Vue.component("resizable-card", ResizableCard);
Vue.component("component-section", ComponentSection);
Vue.component("component-chart", ComponentChart);
Vue.component("component-chart", ComponentChart);
Vue.component("labelmeter", LabelMeter);
Vue.component("circularmeter", CircularMeter);
Vue.component("quartermeter", QuaterMeter);
Vue.component("speedometer", Speedometer);
Vue.component("speedometer-graph", SpeedometerGraph);
Vue.component("speedometer-graph-quarter", SpeedometerGraphQuarter);

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "home",
            component: Home
        },
        {
            path: "/admin/interfaces",
            name: "interfaces-list",
            component: InterfacesList
        },

        {
            path: "/admin/interfaces/:interface_id",
            name: "interfaces-edit",
            component: InterfacesEdit
        },
        {
            path: "/admin/interfaces/create",
            name: "interfaces-create",
            component: InterfacesEdit
        },

        {
            path: "/admin/settings",
            name: "edit_settings",
            component: EditSettings
        },
        {
            path: "/admin",
            redirect: "/admin/interfaces",
        }
    ]
});

Vue.filter('formatDate', function (value) {
    if (value) {
        return moment(new Date(value * 1000).toString()).format('YYYY-MM-DD hh:mm')
    }
});

let globalData = new Vue({
    data: {
        adminConnected: localStorage.getItem("adminConnected")
    },
});
Vue.mixin({
    data: function () {
        return {
            notifications: [],
        };
    },
    computed: {
        adminConnected: {
            get: function () {
                return globalData.$data.adminConnected;
            },
            set: function (newValue) {
                globalData.$data.adminConnected = newValue;
                if (newValue == false) {
                    localStorage.removeItem("adminConnected");
                } else {

                    localStorage.setItem("adminConnected", newValue);
                }
            }
        },
    }
});




import vuetify from './plugins/vuetify' // path to vuetify export
const app = new Vue({
    el: "#app",
    components: { App },
    router,
    vuetify, i18n
});
window.Vue = Vue;
window.app = app;
