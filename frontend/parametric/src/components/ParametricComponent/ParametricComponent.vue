<template>
    <div>
        <PanelComponent
            :isPanelOpen="isPanelOpen"
            :resultButtonText="resultButtonText"
            :noResult="noResult"
        />
        <section :class="['parametric', {'is-ready': isReady}, {'is-overlay': isPanelOpen}]">
            <div class="parametric__content">
                <div class="parametric__title">
                    {{ estateHeading }}
                </div>
                <Result
                    v-if="isReady"
                    :estateHeading="estateHeading"
                    :estatePlural="estatePlural"
                />
            </div>
            <div :class="['parametric__filter']"
                 ref="parametricFilter">
                    <Filters
                        v-if="isReady"
                        ref="filter"
                    />
            </div>
        </section>
    </div>
</template>

<script>
import Filters from '../FiltersComponent/FiltersComponent';
import {mapGetters} from 'vuex';
import PanelComponent from '../PanelComponent/PanelComponent';
import Result from '../ResultComponent/ResultComponent';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'ParametricComponent',

    components: {
        Filters,
        PanelComponent,
        Result
    },

    props: ['isReady', 'isPanelOpen', 'estateHeading', 'estatePlural'],

    data: () => {
        return {
            openFilterButton: null
        };
    },

    computed: {
        ...mapGetters({
            premises: 'GET_PREMISES'
        }),

        resultButtonText() {
            return this.premises.length ?
                `Показать ${this.premises.length} ${this.estateType}` :
                `${this.estateType} не найдено`;
        },

        estateType() {
            return Utils.pluralWord(this.premises.length, this.estatePlural);
        },

        noResult() {
            return this.premises.length === 0;
        }
    }
};
</script>

<style lang="scss">
    @import "ParametricComponent";
</style>

