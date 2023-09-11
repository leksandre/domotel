import type {IDynamicImport} from '@/common/scripts/types/utils';
import type {IYandexMap} from '@/components/yandex-map/types';
import {Utils} from '@/common/scripts/utils';

/**
 * Карта в блоке контакты
 */
let contactsMapWrapper: HTMLElement | null = null;
let isLoaded: boolean = false;

const initMap = (): void => {
    const ymaps = window.ymaps;

    isLoaded = true;

    ymaps.ready(() => {
        import(/* webpackChunkName: "yandex-map" */ '@/components/yandex-map')
            .then(({default: YandexMap}: IDynamicImport) => {
                if (contactsMapWrapper === null) {
                    return;
                }

                const yandexMap: IYandexMap = new YandexMap(ymaps);

                yandexMap.init({
                    wrapper                : contactsMapWrapper,
                    cluster                : true,
                    fullScreenControl      : true,
                    customFullScreenControl: true,
                    customZoomControl      : true
                });
            });
    });
};

const initContactsMap = (contactsMap: HTMLElement): void => {
    contactsMapWrapper = contactsMap;

    if ('IntersectionObserver' in window) {
        const lazyMapObserver = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
            entries.forEach((entry: IntersectionObserverEntry) => {
                if (entry.isIntersecting) {
                    if (!isLoaded) {
                        Utils.checkMap(initMap);
                    }
                }
            });
        }, {rootMargin: '0px 0px 1000px 0px'});

        if (Utils.isInViewport(contactsMap, window.innerHeight || document.documentElement.clientHeight)) {
            Utils.checkMap(initMap);
        } else {
            lazyMapObserver.observe(contactsMap);
        }
    } else {
        Utils.checkMap(initMap);
    }
};

export default initContactsMap;
