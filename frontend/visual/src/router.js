import Building from './pages/BuildingPage/BuildingPage';
import Floor from './pages/FloorPage/FloorPage';
import Router from 'vue-router';
import Vue from 'vue';

Vue.use(Router);
const el = document.getElementById('visual');
const data = {...el.dataset};
const firstStep = data.firstStep ?? 'complex';
const firstComponent = firstStep === 'floor' ? Floor : Building;

export default new Router({
    mode  : 'history',
    base  : process.env.NODE_ENV === 'production' ? data.baseUrl : '/', // eslint-disable-line no-process-env
    routes: [{
        path     : '*/',
        name     : `${firstStep}`,
        component: firstComponent,
        meta     : {
            step: `${firstStep}`,
            id  : `${data.firstStepId}`
        }
    }, {
        path     : '*/building/:buildingId',
        name     : 'building',
        component: Building,
        meta     : {
            step: 'building'
        }
    }, {
        path     : '*/building/:buildingId/section/:sectionId',
        name     : 'sectionBuilding',
        component: Building,
        meta     : {
            step: 'section'
        }
    }, {
        path     : '*/section/:sectionId',
        name     : 'section',
        component: Building,
        meta     : {
            step: 'section'
        }
    }, {
        path     : '*/building/:buildingId/section/:sectionId/floor/:floorId',
        name     : 'floorBuildingSection',
        component: Floor,
        meta     : {
            step: 'floor'
        }
    }, {
        path     : '*/building/:buildingId/floor/:floorId',
        name     : 'floorBuilding',
        component: Floor,
        meta     : {
            step: 'floor'
        }
    }, {
        path     : '*/floor/:floorId',
        name     : 'floor',
        component: Floor,
        meta     : {
            step: 'floor'
        }
    }]
});
