<template>
    <div>
        <div class="interfaces">
            <h2>{{ $t("interfaces.list.title") }}</h2>

            <div
                class="interface"
                v-for="ui in interfaces"
                :key="ui.id"
                @click="editInterface(ui.id)"
            >
                <div class="name" v-text="ui.name"></div>
                <span
                    class="updated_at"
                    v-text="'updated on ' + ui.updated_at"
                ></span>
                <span
                    class="icon material-icons delete"
                    @click.stop="deleteInterface(ui.id)"
                    >delete</span
                >
            </div>

            <v-btn
                tile
                color="primary"
                @click="createNewInterface"
                class="create_new button"
            >
                {{ $t("interfaces.list.create_button") }}
            </v-btn>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.icon {
    padding: 15px 10px;
    transition: 0.3s;
    cursor: pointer;
    vertical-align: middle;
    border-radius: 1px;
}
.icon:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.interfaces {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    padding: 20px;
    box-sizing: border-box;
    background-color: #303030;

    .delete {
        float: right;
        vertical-align: top;
        position: absolute;
        top: 5px;
        right: 5px;
    }
    h2 {
        margin-bottom: 50px;
    }

    .interface {
        padding: 10px;
        border: 2px solid white;
        margin-bottom: 20px;
        transition: 0.3s;
        cursor: pointer;
        position: relative;

        .name {
            font-size: 1.2em;
            font-weight: 400;
        }
        .updated_at {
            font-weight: 200;
            color: rgba(255, 255, 255, 0.851);
        }
    }

    .interface:hover {
        background-color: rgba(255, 255, 255, 0.384);
    }
}

.button {
    padding: 12px 10px;
    margin-top: 5px;
    cursor: pointer;
    border: 0px;
    outline: none;
    border-radius: 1px;
    width: 100%;
    font-weight: 500;
}
.create_new {
    background-color: white;
    color: black;
}
</style>
<script>
export default {
    data() {
        return {
            interfaces: []
        };
    },

    mounted() {
        this.getInterfaces();
    },

    methods: {
        createNewInterface: function() {
            this.$router.push({
                name: "interfaces-create",
                params: { userId: "123" }
            });
        },
        editInterface: function(id) {
            this.$router.push({
                name: "interfaces-edit",
                params: { interface_id: id }
            });
        },
        deleteInterface: function(id) {
            axios.delete("/api/interfaces/" + id).then(res => {
                this.getInterfaces();
            });
        },
        getInterfaces: function() {
            axios.get("/api/interfaces").then(res => {
                this.interfaces = res.data;
            });
        }
    }
};
</script>
