<template>
    <div
        class="result-list__plan"
        @mouseleave="_planClear"
    >
        <!-- Если устройство десктоп, не табличный вид и имеется массив с
        изображениями помещения то выводим карусель изображений -->
        <div v-if="!isMobile && !isRowResultView && itemsSrc?.length"
            :key="listKey"
            class="result-list__plan-dynamic"
        >
            <template v-for="(item, index) in itemsSrc">
                <div v-if="item?.image"
                    v-show="item === itemsSrc[activePlan]"
                    :key="index"
                    class="result-list__plan-item"
                >
                    <img loading="lazy"
                         :src="item?.image"
                         :alt="item?.text" />
                </div>
            </template>
        </div>
        <!-- Иначе выводим одно изображение -->
        <div v-else class="result-list__plan-static">
            <img :src="rawItem.plan[0]?.image"
                 :alt="rawItem.plan[0]?.text || ''">
            <!-- Если не мобильные и табличный вид - выводим дополнительно большое изображение -->
            <div v-if="!isMobile && isRowResultView && !rawItem.plan[0]?.stub"
                 class="result-list__plan-big">
                <img :src="rawItem.plan[0]?.image"
                     :alt="rawItem.plan[0]?.text || ''">
            </div>
        </div>
        <!-- Если есть массив с изображениями и не табличный вид готовим и показываем область с
        полосками доступных изображений -->
        <div v-if="rawItem.plan?.length > 1 && !isRowResultView"
            class="result-list__plan-lines">
            <div v-for="(item, index) in rawItem.plan"
                :key="index"
                 @mouseenter="activePlan = index"
                 class="result-list__plan-line"
            ></div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: 'ResultListPlan',

    props: ['rawItem', 'isRowResultView'],

    data() {
        return {
            // Текущий отображаемые план
            activePlan: 0,
            // массив с загруженными элементами
            itemsSrc  : [],
            // ключ для ререндера при изменении массива
            listKey   : 0
        };
    },

    computed: {
        ...mapGetters({
            isMobile: 'GET_IS_MOBILE'
        })
    },

    beforeMount() {
        if (this.rawItem.plan?.length > 1) {
            this.itemsSrc[0] = this.rawItem.plan[0];
        }
    },

    watch: {
        activePlan() {
            if (this.itemsSrc[this.activePlan]) {
                return;
            }

            this.itemsSrc[this.activePlan] = this.rawItem.plan[this.activePlan];
            this.listKey++;
        }
    },

    methods: {
        _planClear() {
            setTimeout(() => {
                this.activePlan = 0;
            }, 250);
        }
    }
};
</script>

<style lang="scss">
@import "ResultListPlan";
</style>
