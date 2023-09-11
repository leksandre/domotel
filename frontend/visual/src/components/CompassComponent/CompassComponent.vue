<template>
    <div class="visual-compass">
        <div class="visual-compass__container">
            <CompassIcon :style="{transform: `rotate(${compass.degree}deg)`}"
                class-name="visual-compass__compass-icon"/>
        </div>
    </div>
</template>

<script>
import CompassIcon from '../Icons/CompassIcon';
import {mapGetters} from 'vuex';

export default {
    name: 'CompassComponent',

    components: {
        CompassIcon
    },

    computed: {
        ...mapGetters({
            compass: 'GET_COMPASS'
        })
    }
};
</script>

<style lang="scss">
@import "CompassComponent";
</style>
