<template>
  <v-app dark>
    <main-drawer
      v-if="adminConnected"
      :opened="drawerOpened"
      @change-drawer-state="changeDrawerState"
    ></main-drawer>

    <v-app-bar app dark flat>
      <v-app-bar-nav-icon
        v-if="adminConnected"
        @click.stop="changeDrawerState(!drawerOpened)"
      ></v-app-bar-nav-icon>
      <router-link to="/">
        <img
          style="width: 23px; margin-right: 20px"
          src="/img/impedance_logo_simple.png"
        />
      </router-link>
      <v-toolbar-title @click="$router.push('/')" style="cursor: pointer"
        >IMPEDANCE
        <span v-if="settings['main_title']"
          >- {{ settings["main_title"] }}</span
        >
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-select
        width="100px"
        class="mr-4"
        v-if="settings['can_change_interface'] == '1'"
        label="Interfaces"
        v-model="interface_id"
        :items="interfaces"
        item-text="name"
        item-value="id"
        hide-details
        dense
      >
      </v-select>
      <v-btn title="Take a screenshot" @click="takeScreenshot()" icon
        ><v-icon>mdi-monitor-screenshot</v-icon>
      </v-btn>
      <v-btn
        title="Settings"
        v-if="adminConnected"
        @click="$router.push({ name: 'edit_settings' })"
        icon
        ><v-icon>mdi-tune</v-icon>
      </v-btn>

      <v-btn
        title="Administration"
        @click="showAdminDialog = true"
        v-if="!adminConnected"
        icon
        ><v-icon>mdi-login</v-icon>
      </v-btn>
      <v-btn
        title="Se déconnecter"
        @click="adminConnected = false"
        icon
        v-if="adminConnected"
        ><v-icon>mdi-logout</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main style="width: 100%; background: #202020">
      <router-view
        :key="$route.fullPath + $route.query"
        v-bind="{ ui: getCurrentInterface() }"
      ></router-view>
    </v-main>
    <v-snackbar multi-line v-model="displayError" color="error" timeout="3000">
      {{ error.humanMessage }}

      <ul>
        <li v-for="(e, k) in error.errors" :key="'error-' + k">
          <b>{{ k }}</b> : {{ e[0] }}
        </li>
      </ul>
      <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="displayError = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <v-dialog v-model="showAdminDialog" max-width="600">
      <v-card :loading="loading">
        <v-card-title>Connexion Administrateur</v-card-title>

        <v-card-text>
          Afin de paramétrer l'interface local, veuillez saisir le mot de passe
          admin.

          <v-text-field
            type="password"
            label="Mot de passe"
            v-model="password"
            :error="errorMessage != null"
            :error-messages="errorMessage ? errorMessage : ''"
          />
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn :loading="loading" @click="loginAdmin" text
            >Se connecter
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-app>
</template>

<style lang="scss" scoped>
.notifications_list {
  position: fixed;
  display: flex;
  justify-content: flex-end;
  flex-direction: column;
  bottom: 0px;
  right: 0px;
  width: 350px;
  max-width: 100%;
  top: 0px;
  height: 100vh;
  padding: 20px;
  box-sizing: border-box;
  transition: 1s;
  pointer-events: none;
}
</style>
<script>
import MainDrawer from "./MainDrawer.vue";
export default {
  components: { MainDrawer },
  data() {
    return {
      interfaces: [],
      settings: {},
      interface_id: this.$route.query.interface_id,
      displayError: false,
      drawerOpened: true,
      errorMessage: null,
      error: {
        humanMessage: "",
        errors: {},
      },
      showAdminDialog: false,
      password: "",
      loading: false,
    };
  },
  mounted() {
    this.getInterfaces();
    this.getSettings();
    this.errorHandling();
  },
  methods: {
    changeDrawerState(value) {
      this.drawerOpened = value;
    },
    errorHandling() {
      // Add a response interceptor
      axios.interceptors.response.use(
        (response) => {
          // Do something with response data
          return response;
        },
        (error) => {
          console.log(error.response);

          // Do something with response error
          if (error.response.data != undefined) {
            if (error.response.data.message != undefined) {
              this.displayError = true;
              this.error.humanMessage = error.response.data.message;
              this.error.errors = error.response.data.errors;
            }
          }
          return Promise.reject(error);
        }
      );
    },
    takeScreenshot: function () {
      var svgElements = document.body.querySelectorAll("svg");
      svgElements.forEach(function (item) {
        item.setAttribute("width", item.getBoundingClientRect().width);
        item.style.width = null;
      });

      html2canvas(document.querySelector("html"), {
        useCORS: true,
        imageTimeout: 0,
        allowTaint: true,
      }).then((canvas) => {
        var img = canvas.toDataURL("image/png");

        var element = document.createElement("a");
        element.setAttribute("href", img);
        element.setAttribute(
          "download",
          "Screenshot_" + new Date().toString().replace(" ", "_")
        );

        element.style.display = "none";
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
      });
    },
    getInterfaces: function () {
      axios.get("/api/interfaces").then((res) => {
        this.interfaces = res.data;
        if (this.interfaces.length == 0)
          this.$router.push({ name: "interfaces-create" });
      });
    },


    getSettings: function () {
      axios.get("/api/settings").then((res) => {
        this.settings = res.data;
        if (this.interface_id == undefined)
          this.interface_id = parseInt(this.settings["default_interface"]);
        if (this.interface_id == undefined && this.interfaces.length > 0)
          this.interface_id = this.interfaces[0].id;
        console.log(this.settings);
      });
    },

    getCurrentInterface: function () {
      for (var i = 0; i < this.interfaces.length; i++) {
        if (this.interfaces[i].id == this.interface_id)
          return this.interfaces[i];
      }
      return null;
    },
    loginAdmin() {
      this.loading = true;
      axios
        .post("/api/login", {
          password: this.password,
        })
        .then((res) => {
          this.adminConnected = res.data.success;
          if (!res.data.success) {
            this.errorMessage = res.data.message;
          } else {
            this.showAdminDialog = false;
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
  watch: {
    interface_id: function (newVal) {
      this.$router.push({
        name: this.$route.name,
        params: { interface_id: newVal },
        query: { interface_id: newVal },
      });
    },
  },
};
</script>
