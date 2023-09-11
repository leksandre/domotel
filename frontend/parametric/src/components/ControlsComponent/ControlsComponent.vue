<template>
    <div class="parametric-result__controls">
        <ul class="list list_theme_regular list_theme_dot list_theme_relative">
            <li class="list__item">
                Найдено подходящих:&nbsp;<span class="list__item-accent">
                {{ pagination ? pagination.count : premises.length }}
            </span>
            </li>
            <li class="list__item">
                <Sort/>
            </li>
        </ul>
        <ul v-if="resultSwitch" class="list list_theme_regular list_theme_dot">
            <li class="list__item">
                <ButtonItem
                    text="Таблица"
                    :class="['parametric-button_view_button', { 'is-active': resultView === 1 }]"
                    @click="_switchResultView(type = 1)"
                />
            </li>
            <li class="list__item">
                <ButtonItem
                    text="Карточки"
                    :class="['parametric-button_view_button', { 'is-active': resultView === 2 }]"
                    @click="_switchResultView(type = 2)"
                />
            </li>
        </ul>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ButtonItem from '../ButtonItem/ButtonItem';
import Sort from '../SortComponent/SortComponent';

export default {
    name: 'ControlsComponent',

    components: {
        ButtonItem,
        Sort
    },

    props: ['premises'],

    data() {
        return {
            type: null
        };
    },

    computed: {
        ...mapGetters({
            pagination  : 'GET_PAGINATION',
            resultView  : 'GET_RESULT_VIEW',
            resultSwitch: 'GET_RESULT_SWITCH'
        })
    },

    methods: {
        ...mapActions(['changeView']),

        _switchResultView(type) {
            this.changeView({
                type
            });

            this.$nextTick(() => {
                this.$emit('updateAnimation');
            });
        }
    }
};
</script>

<style lang="scss">
@import "../List/List";
</style>
