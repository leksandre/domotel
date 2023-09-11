<template>
    <div
        v-if="perspective?.plan && perspective?.points.length > 1"
        class="visual-rotate">
        <div v-if="!isMobile"
             class="visual-rotate__wrapper">
            <img :src="perspective.plan"
                 alt="План"
                 class="visual-rotate__plan">
            <template v-for="point in perspective.points">
                <button type="button"
                        :key="point.id"
                        :class="['visual-rotate__point', {'is-active': point.id === rotateId}]"
                        :style="{top: `${point.top}%`,
                            left: `${point.left}%`,
                            transform: `rotate(${point.deg}deg)`}"
                        @click="_changeRotateByClick(point.id)">
                    <RotateGradientIcon v-if="point.id === rotateId"
                                        className="visual-rotate__svg-gradient"/>
                    <span class="visual-rotate__circle"></span>
                </button>
            </template>
        </div>
        <button class="visual-rotate__change-button"
                @click="_changeRotateByButton">
            Сменить ракурс
            <RotateIcon class-name="visual-rotate__svg-rotate"/>
        </button>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import RotateGradientIcon from '../Icons/RotateGradientIcon';
import RotateIcon from '../Icons/RotateIcon';

export default {
    name: 'RotateComponent',

    components: {
        RotateGradientIcon,
        RotateIcon
    },

    computed: {
        ...mapGetters({
            isMobile   : 'GET_IS_MOBILE',
            perspective: 'GET_PERSPECTIVE',
            rotateId   : 'GET_ROTATE_ID'
        })
    },

    methods: {
        ...mapActions(['changeRotate']),

        _changeRotateByClick(rotateId) {
            this._setUrl(rotateId);
            this.changeRotate(this.$route.query);
        },

        _changeRotateByButton() {
            const currentPointIndex = this.perspective.points.findIndex((element) => {
                return element.id === this.rotateId;
            });
            const nextId = currentPointIndex > -1 && currentPointIndex !== this.perspective.points.length - 1 ?
                this.perspective.points[currentPointIndex + 1].id :
                this.perspective.points[0].id;

            this._setUrl(nextId);
            this.changeRotate(this.$route.query);
        },

        _setUrl(rotateId) {
            const query = {
                ...this.$route.query,
                ...{rotateId}
            };

            this.$router.push({query});
        }
    }
};
</script>

<style lang="scss">
    @import "RotateComponent";
</style>
