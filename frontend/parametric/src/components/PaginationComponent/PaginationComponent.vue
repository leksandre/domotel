<template>
    <div class="pagination">
        <button v-if="currentPage !== 1"
                type="button"
                class="button"
                @click="$emit('change', currentPage - 1, 'page')">
            <span>Назад</span>
        </button>
        <button v-for="(page, index) in pages"
                :key="index"
                :class="['button-circle', 'pagination__button', {'is-active': page.isActive}]"
                :disabled="page.isActive"
                @click="$emit('change', page.name, 'page')">
            <span>{{ page.name }}</span>
        </button>
        <button v-if="currentPage !== this.pagination.pages"
                type="button"
                class="button"
                @click="$emit('change', currentPage + 1, 'page')">
            <span>Дальше</span>
        </button>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';

export default {
    name: 'PaginationComponent',

    props: ['form'],

    data() {
        return {
            currentPage: 1
        };
    },

    computed: {
        ...mapGetters({
            isMobile  : 'GET_IS_MOBILE',
            page      : 'GET_PAGE',
            pagination: 'GET_PAGINATION'
        }),

        isLastPage() {
            return this.pagination.currentPage * this.pagination.limit < this.pagination.count;
        },

        maxCount() {
            return this.isMobile ? 3 : 5;
        },

        startPage() {
            const delta = this.isMobile ? 1 : 2;

            if (this.currentPage - delta <= 0) {
                return 1;
            } else if (this.currentPage + delta >= this.pagination.pages) {
                if (this.pagination.pages < this.maxCount) {
                    return this.pagination.pages - 1;
                }

                return this.pagination.pages - this.maxCount + 1;
            }

            return this.currentPage - delta;
        },

        pages() {
            const range = [];

            for (
                let i = this.startPage;
                i <= Math.min(this.startPage + this.maxCount - 1, this.pagination.pages);
                i++
            ) {
                range.push({
                    name    : i,
                    isActive: i === this.currentPage
                });
            }

            return range;
        }
    },

    watch: {
        page() {
            this.currentPage = this.page;
        }
    },

    mounted() {
        this._setStartPage();
    },

    methods: {
        ...mapActions(['changePaginationMode', 'showPage']),

        _setStartPage() {
            if (this.page > this.pagination.pages) {
                const searchParams = new URLSearchParams(window.location.search);

                searchParams.delete('page');
                window.history.replaceState(null, null, `?${searchParams.toString()}`);
            } else {
                this.currentPage = this.page;
            }
        }
    }
};
</script>

<style lang="scss">
    @import "PaginationComponent.scss";
</style>
