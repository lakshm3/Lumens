<template>
    <v-row justify="center">
        <v-dialog max-width="800" scrollable persistent v-bind:value="value">
            <v-card>
                <v-card-title>Trend Setup</v-card-title>
                <v-card-text>
                    <v-text-field
                        label="Name"
                        v-model="extras.name"
                    ></v-text-field>
                    <v-select
                        v-model="extras.lateralIndicators"
                        label="Lateral Indicators"
                        item-value="key"
                        :items="allindicators"
                        persistent-hint
                        multiple
                        hint="Les indicateurs apparaitront dans l'ordre de sélection de
                    cette liste. Si le nombre d'indicateurs dépasse 5, les
                    excédants seront choisissable au sein d'une liste déroulante"
                    >
                        <template slot="selection" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                        <template slot="item" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                    </v-select>

                    <v-select
                        v-model="extras.graphIndicators"
                        label="Graph Indicators"
                        item-value="key"
                        multiple
                        :items="allindicators"
                        persistent-hint
                        hint="Maximum 4 indicateurs"
                    >
                        <template slot="selection" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                        <template slot="item" slot-scope="data">
                            {{ $t("indicators." + data.item.key) }}
                        </template>
                    </v-select>
                    <v-switch
                        v-model="extras.comparative_mode"
                        label="Ajouter un indicateur comparatif sur le graphique"
                    ></v-switch>
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
                name: "",
                comparative_mode: false,
                lateralIndicators: [],
                graphIndicators: []
            }
        };
    },

    mounted() {
        //this.extras = this["default-extras"];
    },
    watch: {
        value: function(newValue) {
            if (this.defaultExtras) this.extras = this.defaultExtras;
        }
    }
};
</script>
