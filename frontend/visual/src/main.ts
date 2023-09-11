import type {NavigationGuardNext, Route} from 'vue-router';
import App from './App.vue';
import type {CreateElement} from 'vue';
import router from './router';
import store from './store';
import Vue from 'vue';
import Vue2TouchEvents from 'vue2-touch-events';

Vue.use(Vue2TouchEvents);
Vue.config.productionTip = false;

const id: string = '#visual';
const visual: HTMLElement | null = document.querySelector(id);

if (visual) {
    Vue.prototype.$dataset = {...visual.dataset};

    router.beforeEach((to: Route, from: Route, next: NavigationGuardNext) => {
        for (const param in Vue.prototype.$dataset) {
            if (!Object.prototype.hasOwnProperty.call(to.params, param)) {
                to.params[param] = Vue.prototype.$dataset[param];
            }
        }
        next();
    });

    new Vue({
        router,
        store,
        render: (h: CreateElement) => {
            return h(App);
        }
    }).$mount(visual);
}
