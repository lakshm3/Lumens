<template>
    <div class="settings" style="padding:30px">
        <v-row>
            <v-col>
                <v-card :loading="loading_settings">
                    <v-card-title>General Settings</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="saveSettings">
                            <v-text-field
                                label="Main title"
                                v-model="settings['main_title']"
                                hint="title appear in the header of the dashboard"
                            >
                            </v-text-field>

                            <v-select
                                v-model="settings['default_interface']"
                                label="Default Displayed interface"
                                :items="interfaces"
                                item-text="name"
                                item-value="id"
                                hint="Choose the default displayed interface"
                            >
                            </v-select>

                            <v-select
                                label="Setup interface"
                                v-model="settings['can_setup_interface']"
                                :items="[
                                    { name: 'Allow', value: '1' },
                                    { name: 'Disallow', value: '0' }
                                ]"
                                item-text="name"
                                item-value="value"
                                hint="Enable if you want allow user to create/edit an
                    interface"
                            >
                            </v-select>

                            <v-select
                                label="Change Displayed interface"
                                v-model="settings['can_change_interface']"
                                :items="[
                                    { name: 'Allow', value: '1' },
                                    { name: 'Disallow', value: '0' }
                                ]"
                                item-text="name"
                                item-value="value"
                                hint="Enable if you want allow user to change interface amoung
                    the one you already designed"
                            >
                            </v-select>

                            <v-text-field
                                v-model="settings['api_key']"
                                hint="Unique API Key which allow to publish data on the remote platform"
                            >
                            </v-text-field>

                            <v-select
                                width="50px"
                                class="mr-4"
                                label="Language"
                                v-model="currentLocale"
                                :items="langs"
                                item-text="name"
                                item-value="code"
                            >
                            </v-select>

                            <v-btn
                                tile
                                type="submit"
                                color="primary"
                                :loading="loading_settings"
                                :disabled="loading_settings"
                            >
                                SAVE SETTINGS
                            </v-btn>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col>
                <v-card :loading="loading_engines">
                    <v-card-title>Vibox Settings</v-card-title>
                    <v-data-table
                        :loading="loading_engines"
                        :headers="headers"
                        :items="engines"
                        :items-per-page="10"
                        class="elevation-1"
                    >
                        <template v-slot:item.last_measure="{ item }">
                            {{
                                item.measure
                                    ? item.measure.DateAndTime
                                    : "No measure yet"
                            }}
                        </template>
                        <template v-slot:item.last_status="{ item }">
                            {{
                                item.measure
                                    ? item.measure.Status
                                    : "No measure yet"
                            }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn color="red" @click="deleteEngine(item.id)">
                                <v-icon left>mdi-delete</v-icon>Delete</v-btn
                            >
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
<style lang="scss" scoped>
.settings_section {
    display: inline-block;
    vertical-align: top;
    h1 {
        padding-left: 10px;
    }
    h2 {
        margin-top: 10px;
    }
}

table {
    th {
        font-size: 1.4em;
        font-weight: 500;
        color: white;
    }
    td,
    th {
        padding: 20px;
    }
    td {
        padding: 12px 20px;
    }
}
.save_settings {
    cursor: pointer;
    padding: 10px 12px;
    border-radius: 3px;
    outline: none;
    border: none;
}
small {
    margin-top: 5px;
    color: rgba(255, 255, 255, 0.7);
    display: block;
}
</style>
<script>
export default {
    data() {
        return {
            headers: [
                {
                    text: "Id",
                    value: "id"
                },
                {
                    text: "Name",
                    value: "name"
                },
                {
                    text: "Process Id",
                    value: "engine_vib_id"
                },
                {
                    text: "Process Type",
                    value: "process_type"
                },
                {
                    text: "Last Measure",
                    value: "last_measure"
                },
                {
                    text: "Last Status",
                    value: "last_status"
                },
                {
                    text: "Updated at",
                    value: "updated_at"
                },
                {
                    text: "Actions",
                    value: "actions"
                }
            ],
            settings: {},
            interfaces: [],
            engines: [],
            loading_settings: false,
            loading_engines: false
        };
    },

    mounted() {
        this.getSettings();
        this.getInterfaces();
        this.getEngines();
    },

    methods: {
        deleteEngine: function(engine_id) {
            var confirmation = confirm(
                "Are you sure, you want to delete this engine ?"
            );
            if (confirmation) {
                axios.delete("/api/engines/" + engine_id).then(res => {
                    this.getEngines();
                });
            }
        },
        getSettings: function() {
            this.loading_settings = true;
            axios.get("/api/settings").then(res => {
                if (Object.keys(res.data).length > 0) this.settings = res.data;
                this.loading_settings = false;
            });
        },
        getEngines: function() {
            this.loading_engines = true;
            axios.get("/api/engines").then(res => {
                this.engines = res.data;
                this.loading_engines = false;
            });
        },

        getInterfaces: function() {
            axios.get("/api/interfaces").then(res => {
                this.interfaces = res.data;
            });
        },

        saveSettings: function() {
            console.log(JSON.parse(JSON.stringify(this.settings)));

            this.loading_settings = true;
            axios
                .post("/api/settings", JSON.stringify(this.settings))
                .then(res => {
                    this.loading_settings = false;
                    this.settings = res.data;
                })
                .catch(err => {
                    this.loading_settings = false;
                });
        }
    }
};
</script>
