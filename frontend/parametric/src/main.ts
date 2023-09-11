import App from './App.vue';
import type {CreateElement} from 'vue';
import store from './store';
import Vue from 'vue';

const id = '#parametric';
const parametricElement: HTMLElement | null = document.querySelector(id);

if (parametricElement) {
    Vue.prototype.$appOptions = {...parametricElement.dataset};

    new Vue({
        store,
        render: (h: CreateElement) => {
            return h(App);
        }
    }).$mount(id);
}
