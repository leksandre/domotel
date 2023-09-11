import type {IPlanoplan} from './types';
import {Utils} from '@/common/scripts/utils';

class InitPlanoplan implements IPlanoplan {
    private element: HTMLElement;

    constructor(element: HTMLElement) {
        this.element = element;
    }

    public init(): void {
        // Если скрипт планоплана уже есть на странице, то запускаем загрузку виджета, если нет, подгружаем скрипт
        if (window.Planoplan) {
            this._load();
        } else {
            Utils.loadScript('https://widget.planoplan.com/etc/multiwidget/release/static/js/main.js', this._load.bind(this));
        }
    }

    // Загружаем виджет планоплана
    private _load(): void {
        const widgetDataEncode = this.element.dataset['planoplan'];
        const widgetId = this.element.id;

        if (!widgetDataEncode) {
            throw new Error(`#${widgetId} должен содержать атрибут 'data-planoplan' с данными виджета`);
        }

        const widgetData = JSON.parse(atob(widgetDataEncode));

        window.Planoplan.init({
            el             : widgetId,
            uid            : widgetData.uid,
            primaryColor   : widgetData.primaryColor,
            secondaryColor : widgetData.secondaryColor,
            textColor      : widgetData.textColor,
            backgroundColor: widgetData.backgroundColor
        });
    }
}

export default InitPlanoplan;
