<template>
    <div class="loader">
        <div class="loader__item"></div>
        <div class="loader__item"></div>
        <div class="loader__item"></div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: 'PreloaderComponent',

    computed: {
        ...mapGetters({
            isReady: 'GET_IS_READY'
        })
    }
};
</script>

<style lang="scss">
    @import "../../../../src/components/loader/loader";
</style>
