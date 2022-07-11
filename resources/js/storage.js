import Vue from 'vue'
import i18n from './translations'
import indicators from "./views/lumens_components/data/Indicators.json";

var userLang = navigator.language || navigator.userLanguage;
if (userLang != null && userLang.indexOf("-") != -1) userLang = userLang.substring(0, userLang.indexOf("-"));
if (userLang != "en" && userLang != "fr") userLang = "en";

export default new Vue({
  data: {
    user: getDataItem("user", null),
    currentLocale: getDataItem("lang", userLang),
    globalConfirmDialog: { show: false, title: "", callback: null },
    allindicators: indicators
  },

});

function getDataItem(name, defaultValue) {
  console.log("Ask for " + name + " in localStorage");
  if (defaultValue === undefined) defaultValue = [];
  var data = localStorage.getItem(name);
  if (data == null) return defaultValue;
  return JSON.parse(data);
}
function setDataItem(name, data) {
  localStorage.setItem(name, JSON.stringify(data));
}

function getCacheItem(name, defaultValue) { return getDataItem("cache_" + name, defaultValue); }
function setCacheItem(name, data) { setDataItem("cache_" + name, data) }
function removeAllCacheItems() {
  for (var i = 0; i < localStorage.length; i++) {
    if (localStorage.key(i).startsWith("cache_")) localStorage.removeItem(localStorage.key(i));
  }
}


Vue.mixin({

  data() {
    return {
      gloading: false,
      langs: [
        {
          name: "Français",
          code: "fr",
        },
        {
          name: "English",
          code: "en",
        },
      ],
    }
  },

  methods: {
    confirmClick(title, clickFunction) {
      this.globalConfirmDialog.title = title;
      this.globalConfirmDialog.callback = clickFunction;
      this.globalConfirmDialog.show = true;
    },
    capchaClick(e, clickFunction) {
      e.preventDefault();
      window.grecaptcha.ready(function () {
        window.grecaptcha
          .execute("6LcmHuMZAAAAAI0lV34F2IiXQPMQfMu8TlAlMWq2", {
            action: "submit",
          })
          .then(function (token) {
            clickFunction(token);
          });
      });
    },
    tsToDate(UNIX_timestamp) {
      var a = new Date(UNIX_timestamp * 1000);
      var months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"
      ];
      var year = a.getFullYear();
      var month = months[a.getMonth()];
      var date = a.getDate();
      var hour = a.getHours();
      var min = a.getMinutes();
      var sec = a.getSeconds();
      var time =
        date +
        " " +
        month +
        " " +
        year +
        " " +
        hour +
        ":" +
        min +
        ":" +
        sec;
      return time;
    },
    addParamsToLocation(params) {
      history.pushState(
        {},
        null,
        this.$route.path +
        '?' +
        Object.keys(params)
          .map(key => {
            return (
              encodeURIComponent(key) + '=' + encodeURIComponent(params[key])
            )
          })
          .join('&')
      )
    },
    withBrTags: function (text) {
      if (!text) return "";
      return text.replace(/(\\r)*\\n/g, '<br>')
    },
    setCacheItem: function (name, data) {
      setCacheItem(name, data);
    },
    findIndicatorForKey(key) {
      return this.allindicators.find((i) => i.key == key);
    },

    getCacheItem: function (name, defaultValue) {
      return getCacheItem(name, defaultValue);
    },

    removeAllCacheItems: function () {
      removeAllCacheItems();
    },
    humanFileSize(bytes, si = false, dp = 1) {
      const thresh = si ? 1000 : 1024;

      if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
      }

      const units = si ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'] : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
      let u = -1;
      const r = 10 ** dp;

      do {
        bytes /= thresh;
        ++u;
      } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


      return bytes.toFixed(dp) + ' ' + units[u];
    },
    logout() {
      this.user = null;
      localStorage.removeItem("user");
      localStorage.removeItem("access_token");
      localStorage.removeItem("refresh_token");

      this.removeAllCacheItems();

      if (this.$route.matched.some((m) => m.meta.requiresAuth))
        this.$router.push("/");
      //axios.post("/logout");
    },
  },
  computed: {
    allindicators: {
      get: function () {
        return globalData.$data.allindicators;
      }
    },
    currentLocale: {
      get: function () {
        return globalData.$data.currentLocale;
      },
      set: function (newLang) {
        i18n.locale = newLang;
        globalData.$data.currentLocale = newLang;
        if (newLang != null) {
          localStorage.setItem("lang", JSON.stringify(newLang));
        }
      },
    },
    user: {
      get: function () {
        return globalData.$data.user;
      },
      set: function (newUser) {
        globalData.$data.user = newUser;
        if (newUser != null) {
          localStorage.setItem("user", JSON.stringify(newUser));
        }
      },
    },
    globalConfirmDialog: {
      get: function () {
        return globalData.$data.globalConfirmDialog;
      },
      set: function (globalConfirmDialog) {
        globalData.$data.globalConfirmDialog = globalConfirmDialog;
      },
    }

  },
});
