import type {IYandexMapFilter, IYandexMapFilterSettings} from '../types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';

const observer: IObserver = new Observer();

class MapFilter implements IYandexMapFilter {
    public globalFilterState: boolean = true;
    private buttons: HTMLInputElement[] = [];
    private readonly checkEvent: Event;

    /**
     * Базовые свойства
     * @constructor
     */
    constructor() {
        this.checkEvent = new Event('change', {
            bubbles   : false,
            cancelable: true});
        document.dispatchEvent(this.checkEvent);
    }

    /**
     * Инициализирует плагин
     * @param {Object} outerSettings - настройки из внешнего файла
     */
    public init(outerSettings: IYandexMapFilterSettings): void {
        this.buttons = [...outerSettings.target.querySelectorAll('.j-map-filter-item')] as HTMLInputElement[];

        this._bindEvents();
    }

    public changeCheckBoxState(): void {
        const holdGlobalFilterState = this.globalFilterState;

        this.buttons.forEach((button: HTMLInputElement) => {
            if (button.checked !== holdGlobalFilterState) {
                return;
            }

            button.checked = !button.checked;
            button.dispatchEvent(this.checkEvent);
        });
    }

    /**
     * Навешивает обработчик клика
     */
    private _bindEvents(): void {
        this.buttons.forEach((button: HTMLInputElement) => {
            button.addEventListener('change', (event: Event) => {
                const mapEvent = button.checked ? 'filterMapItem:add' : 'filterMapItem:remove';

                this._setGlobalFilterState();

                observer.publish(mapEvent, (event.target as HTMLElement).dataset['type']);
                observer.publish('filterMap:updated');
            });
        });
    }

    private _setGlobalFilterState(): void {
        this.globalFilterState = this.buttons.every((button: HTMLInputElement) => {
            return button.checked;
        });
    }
}

export default MapFilter;
