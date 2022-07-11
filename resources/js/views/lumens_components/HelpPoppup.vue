<template>
    <div
        class="help"
        :style="{ top: top + 'px', left: left + 'px' }"
        v-if="help"
    >
        <h1 v-text="help.title"></h1>
        <h2 v-if="help.measured != undefined && help.measured.length > 0">
            {{ $t("help.description") }}
        </h2>
        <li v-for="(r, i) in help.measured" v-text="r" :key="'obs' + i"></li>
        <h2
            v-if="
                help.observations != undefined && help.observations.length > 0
            "
        >
            {{ $t("help.observations") }}
        </h2>
        <li
            v-for="(r, i) in help.observations"
            v-text="r"
            :key="'ras' + i"
        ></li>
        <h2
            v-if="
                help.potential_problems != undefined &&
                    help.potential_problems.length > 0
            "
        >
            {{ $t("help.potential_problems") }}
        </h2>
        <li
            v-for="(r, i) in help.potential_problems"
            v-text="r"
            :key="'prob' + i"
        ></li>
        <h2 v-if="help.actions != undefined && help.actions.length > 0">
            {{ $t("help.actions") }}
        </h2>
        <li
            v-for="(r, i) in help.actions"
            v-text="r"
            :key="'to_monitor' + i"
        ></li>
    </div>
</template>
<style scoped>
li {
    margin-bottom: 5px;
}
</style>
<script>
export default {
    props: ["help", "top", "left"]
};
</script>
