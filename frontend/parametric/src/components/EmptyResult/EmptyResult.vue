<template>
    <div class="empty-result">
        <EmptyResultIcon
            :class="['empty-result__icon']"
        />
        <div class="empty-result__title">
            Вы совершили невозможное!
        </div>
        <div class="empty-result__text">
            {{ estateType }} не найдены.
            Пожалуйста, измените параметры фильтра.
        </div>
    </div>
</template>

<script>
import EmptyResultIcon from '../Icons/EmptyResultIcon';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'EmptyResult',

    props: ['estatePlural'],

    components: {
        EmptyResultIcon
    },

    computed: {
        estateType() {
            return Utils.pluralWord(2, this.estatePlural);
        }
    }
};
</script>

<style lang="scss">
@import "EmptyResult";
</style>
