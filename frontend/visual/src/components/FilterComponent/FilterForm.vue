<template>
    <form class="visual-filters__form"
          ref="form"
          @change="change">
        <div class="visual-filters__list">

            <CheckboxItem v-for="(item, index) in roomsFilter.items"
                          :key="index"
                          :name="`room[${item.id}]`"
                          :checked="item.checked"
                          :color="item.color"
                          :disabled="item.disabled"
                          :value="item.id"
                          :title="item.title"
                          :modify="'checkbox_theme_filter'"
                          @change="changeCheckbox(item)"
            />
        </div>
    </form>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import CheckboxItem from '../CheckboxItem/CheckboxItem';

export default {
    name: 'FilterForm',

    components: {
        CheckboxItem
    },

    props: {
        items: Object
    },

    computed: {
        ...mapGetters({
            dataStep   : 'GET_DATA_STEP',
            roomsFilter: 'GET_ROOMS_FILTER'
        })
    },

    methods: {
        ...mapActions(['changeFilter']),

        change() {
            this._setURLParams();
            this.changeFilter({
                form : this.$refs.form,
                query: this.$route.query
            });
        },

        _setURLParams() {
            const formData = new FormData(this.$refs.form);
            const filtersData = {};
            const rotate = this.$route.query.rotateId ? {rotateId: this.$route.query.rotateId} : {};

            formData.forEach((value, key) => {
                filtersData[key] = value;
            });

            const query = {
                ...filtersData,
                ...rotate
            };

            this.$router.push({query});
        },

        changeCheckbox(item) {
            item.checked = !item.checked;
        }
    }
};
</script>
