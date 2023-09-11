import InitPlanoplan from '@/components/planoplan';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IPlanoplan} from '../planoplan/types';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();

// Проверяем - находится ли планоплан в Табе и активен ли таб.
// Если Планоплан не в табе, то сразу стартуем его при вызове этой функции.
const checkTabs = (parent: HTMLElement, planoplan: IPlanoplan): void => {
    if (parent && !parent.classList.contains('is-active')) {
        const parentTab = parent.dataset['tab'];

        observer.subscribe('tabChange', (element: HTMLElement): void => {
            if (element.dataset['tab'] !== parentTab) {
                return;
            }

            planoplan.init();
        });
    } else {
        planoplan.init();
    }
};

const initPlanoplanWidgets = (planoplanElements: HTMLElement[]): void => {
    planoplanElements.forEach((widget: HTMLElement) => {
        const parent = widget.closest('.j-tabs-content__item') as HTMLElement;
        const planoplan: IPlanoplan = new InitPlanoplan(widget);

        if (!parent) {
            return;
        }

        if ('IntersectionObserver' in window) {
            const widgetObserve = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
                entries.forEach((entry: IntersectionObserverEntry) => {
                    if (entry.isIntersecting) {
                        checkTabs(parent, planoplan);
                    }
                });
            });

            if (Utils.isInViewport(widget,
                window.innerHeight || document.documentElement.clientHeight)) {
                checkTabs(parent, planoplan);
            } else {
                widgetObserve.observe(widget);
            }
        } else {
            checkTabs(parent, planoplan);
        }
    });
};

export default initPlanoplanWidgets;
