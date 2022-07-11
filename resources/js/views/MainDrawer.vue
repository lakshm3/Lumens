<template>
    <v-navigation-drawer @input="onInputDrawer" app :value="opened" v-if="adminConnected">
        <v-list nav>
            <v-list-item to="/" link>
                <v-list-item-icon>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{
                        $t("drawer.home")
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item :to="{name:'interfaces-list'}" link>
                <v-list-item-icon>
                    <v-icon> mdi-monitor-dashboard </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{
                        $t("drawer.interfaces")
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item  :to="{name:'edit_settings'}" link>
                <v-list-item-icon>
                    <v-icon> mdi-tune </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{
                        $t("drawer.settings")
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<style scoped></style>
<script>
export default {
    data: function() {
        return {
            canClose: false
        };
    },
    props: ["opened"],
    methods: {
        onInputDrawer(value) {
            this.$emit("change-drawer-state", value);
        },
        close: function() {
            console.log("close");
            if (this.canClose) this.$emit("close");
        }
    }
};
</script>
