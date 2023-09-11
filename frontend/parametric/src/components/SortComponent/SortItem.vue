<template>
    <div>
        <ButtonItem :disabled="disabled"
                    :modify="modify"
                    :text="text"
                    :type="type"
                    :order="order"
                    @click="_setSort(type, order)"
        />
    </div>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import {mapActions} from 'vuex';

export default {
    name: 'SortItem',

    components: {
        ButtonItem
    },

    props: ['disabled', 'modify', 'text', 'type', 'order'],

    methods: {
        ...mapActions(['changeSort']),

        _setSort(type, order) {
            this.changeSort({
                type,
                order
            });
        }
    }
};
</script>

<style lang="scss">
@import "SortComponent";
</style>
