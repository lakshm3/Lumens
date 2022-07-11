<template>
    <v-row justify="center">
        <v-dialog max-width="800" scrollable persistent v-bind:value="value">
            <v-card>
                <v-card-title>EDS Setup</v-card-title>
                <v-card-text>
                    <v-select
                        v-model="extras.meanIndicators"
                        label="Indicateurs moyens"
                        item-value="key"
                        multiple
                        :items="allindicators"
                    >
                        <template slot="selection" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                        <template slot="item" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                    </v-select>

                    <v-select
                        v-model="extras.detailIndicators"
                        label="Indicateurs détaillés par cylindre"
                        item-value="key"
                        multiple
                        :items="allindicators"
                        hint="Ces indicateurs sont détaillés par cylindres sous forme de ligne"
                    >
                        <template slot="selection" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                        <template slot="item" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                    </v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="$emit('close', extras)">OK</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<script>
export default {
    props: ["value", "defaultExtras"],
    data() {
        return {
            extras: {
                meanIndicators: [],
                detailIndicators: []
            }
        };
    },

    mounted() {
        //this.extras = this["default-extras"];
    },
    watch: {
        value: function(newValue) {
            if (this.defaultExtras) this.extras = this.defaultExtras;
            console.log(this.defaultExtras);
            console.log(this.defaultExtras);
            //this.extras = newValue;
        }
    }
};
</script>
