<template>
    <div class="visual-pinchzoom"
         v-touch:start="bindClick">
        <div class="visual-pinchzoom__container">
            <ZoomHandIcon
                :class-name="'visual-pinchzoom__svg'"
            />
        </div>
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import ZoomHandIcon from '../Icons/ZoomHandIcon';

export default {
    name: 'PinchzoomComponent',

    components: {
        ZoomHandIcon
    },

    methods: {
        ...mapActions(['changeStatePinch']),

        bindClick() {
            this.changeStatePinch(false);
        }
    }
};
</script>

<style lang="scss">
@import 'PinchzoomComponent';
</style>
