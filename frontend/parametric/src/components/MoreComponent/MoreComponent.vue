<template>
  <div class="parametric-result__more">
      <ButtonItem
          :text="`Загрузить ещё ${itemsToLoad} ${this.estateType} из ${this.hiddenPremisesCount}`"
          class="parametric-button__more-button parametric-button_full-width_tablet"
          @click="nextPage"
      />
  </div>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import {mapGetters} from 'vuex';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'MoreComponent',

    components: {
        ButtonItem
    },

    props: {
        hiddenPremisesCount: {
            type    : Number,
            required: true
        },
        visiblePremisesCount: {
            type    : Number,
            required: true
        },
        estatePlural: {
            type    : Array,
            required: true
        }
    },

    computed: {
        ...mapGetters({
            page: 'GET_PAGE'
        }),

        itemsToLoad() {
            return this.hiddenPremisesCount < this.visiblePremisesCount ?
                this.hiddenPremisesCount :
                this.visiblePremisesCount;
        },

        estateType() {
            return Utils.pluralWord(this.itemsToLoad, this.estatePlural);
        }
    },

    methods: {
        nextPage() {
            this.$root.$emit('nextPage');
        }
    }
};
</script>
