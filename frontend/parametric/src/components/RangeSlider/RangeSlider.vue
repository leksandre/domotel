<template>
    <div :class="['range-slider', {'is-active': isChange}]">
        <div class="range-slider__container">
            <div class="range-slider__range">
                <span class="range-slider__desc">от&nbsp;</span>
                <span class="range-slider__value">{{ convertToDigit(lowerRange) }}</span>
            </div>
            <div class="range-slider__range">
                <span class="range-slider__desc">до&nbsp;</span>
                <span class="range-slider__value">{{ convertToDigit(upperRange) }}</span>
            </div>
        </div>

        <VueSlider
            v-model="slider.value"
            :min="slider.min"
            :max="slider.max"
            :enableCross="true"
            :interval="setStepRange(sliderType)"
            @drag-end="changeFilters"
            @change="change"
        />

        <input v-if="isChangeDone" type="hidden"
               :data-counter="true"
               :data-min="slider.min"
               :data-min-value="lowerRange * (slider.multiply || 1)"
               :data-max="slider.max"
               :data-max-value="upperRange * (slider.multiply || 1)">

        <input v-if="isChangeDone"
               type="hidden" :name="`${sliderType}[min]`" :value="lowerRange * (slider.multiply || 1)">
        <input v-if="isChangeDone"
               type="hidden" :name="`${sliderType}[max]`" :value="upperRange * (slider.multiply || 1)">
    </div>
</template>

<script>
import 'vue-slider-component/theme/default.css';
import {Utils} from '@/common/scripts/utils';
import VueSlider from 'vue-slider-component';

export default {
    name: 'RangeSlider',

    components: {
        VueSlider
    },

    props: ['slider', 'sliderType'],

    data() {
        return {
            isChange    : false,
            isChangeDone: false
        };
    },

    computed: {
        lowerRange() {
            return this.slider.value[0];
        },

        upperRange() {
            return this.slider.value[1];
        }
    },

    methods: {
        setStepRange(type) {
            return type === 'price' ? 1000 : 1;
        },

        convertToDigit(price) {
            return Utils.convertToDigit(price);
        },

        change() {
            this.isChange = true;
        },

        changeFilters() {
            this.isChangeDone = true;

            // если после изменения слайдера оба значения равны min и max, снимаем активность
            if (this.isChangeDone) {
                if (this.lowerRange === this.slider.min && this.upperRange === this.slider.max) {
                    this.isChange = false;
                }
            }

            this.$nextTick(() => {
                this.$emit('change', this.slider);
            });
        },

        reset() {
            this.isChange = false;
            this.isChangeDone = false;
        }
    }
};
</script>

<style lang="scss">
@import "RangeSlider";
</style>

