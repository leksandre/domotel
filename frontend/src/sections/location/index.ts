/**
 * Зависимости
 */
import type {IYandexMap, IYandexMapFilter} from '@/components/yandex-map/types';
import type {IDynamicImport} from '@/common/scripts/types/utils';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();

/**
 * Карта в блоке Расположение
 */
let locationMap: HTMLElement | null = null;
const filterListButtons = [...document.querySelectorAll('.j-location-filter-list-toggle')] as HTMLElement[];
const filterList = document.querySelector('.j-location-infrastructure') as HTMLElement;
const filterCheckboxToggler = document.querySelector('.j-checkbox-toggle') as HTMLElement;
const mapFilterWrapper = document.querySelector('.j-map-filter') as HTMLElement;
const mapFilterContent = document.querySelector('.j-map-filter__content') as HTMLElement;

let isLoaded = false;
// Функция слушателя отвечающая за отслеживание нажатия кнопки Escape на клавиатуре, при открытом фильтре Инфраструктуры
const escapeWatcher = (event: Event): void => {
    if ((event as KeyboardEvent).key === 'Escape' && filterList?.classList.contains('is-open')) {
        // event.target - элемент кнопки фильтра
        toggleFilter();
    }
};

// Открывает-закрывает и навешивает слушатель при открытии фильтра
const toggleFilter = (): void => {
    filterList?.classList.toggle('is-open');
    filterListButtons.forEach((item: HTMLElement) => {
        item.classList.toggle('is-open');
    });

    // Вешаем слушателя
    let method = 'addEventListener';

    if (filterList?.classList.contains('is-open')) {
        // Фиксируем скролл страницы
        Utils.isMobile() && Utils.bodyFixed(mapFilterContent);
    } else {
        // Удаляем слушателя
        method = 'removeEventListener';
        // Снимаем фикс со страницы
        Utils.isMobile() && Utils.bodyStatic();
    }

    // @ts-ignore
    document[method]('keydown', escapeWatcher);
};

const initLocationMap = (): void => {
    isLoaded = true;

    if (mapFilterWrapper) {
        import(/* webpackChunkName: "map-filter" */ '@/components/yandex-map/infrastructure-checkbox-filter')
            .then(({default: MapFilter}: IDynamicImport) => {
                const mapFilter: IYandexMapFilter = new MapFilter();

                mapFilter.init({target: mapFilterWrapper});

                if (filterCheckboxToggler) {
                    observer.subscribe('filterMap:updated', () => {
                        filterCheckboxToggler.innerHTML = mapFilter.globalFilterState ? 'Скрыть все' : 'Показать все';
                    });

                    filterCheckboxToggler.addEventListener('click', () => {
                        mapFilter.changeCheckBoxState();
                    });
                }
            });
    }

    if (filterListButtons && filterList) {
        filterListButtons.forEach((item: Element) => {
            (item as HTMLElement).addEventListener('click', () => {
                toggleFilter();
            });
        });
    }

    const ymaps = window.ymaps;

    if (ymaps) {
        ymaps.ready(() => {
            import(/* webpackChunkName: "yandex-map" */ '@/components/yandex-map')
                .then(({default: YandexMap}: IDynamicImport) => {
                    if (locationMap === null) {
                        return;
                    }

                    const yandexMap: IYandexMap = new YandexMap(ymaps);

                    yandexMap.init({
                        wrapper                : locationMap,
                        cluster                : true,
                        fullScreenControl      : true,
                        customFullScreenControl: true,
                        customZoomControl      : true
                    });
                });
        });
    }
};

const initLocation = (locationWrapper: HTMLElement): void => {
    locationMap = locationWrapper;

    if ('IntersectionObserver' in window) {
        const lazyImageObserver = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
            entries.forEach((entry: IntersectionObserverEntry) => {
                if (entry.isIntersecting) {
                    if (!isLoaded) {
                        Utils.checkMap(initLocationMap);
                    }
                }
            });
        }, {rootMargin: '0px 0px 1000px 0px'});

        if (Utils.isInViewport(locationMap, window.innerHeight || document.documentElement.clientHeight)) {
            Utils.checkMap(initLocationMap);
        } else {
            lazyImageObserver.observe(locationMap);
        }
    } else {
        Utils.checkMap(initLocationMap);
    }
};

export default initLocation;
