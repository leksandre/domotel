import type {IMobileNavigation, IMobileNavigationMenu, IMobileNavigationOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';

const observer: IObserver = new Observer();
const CLASS_ACTIVE = 'is-active';
// Селектор всех меню и подменю
const SELECTOR_MOBILE = '.j-navigation-mobile__menu';

class MobileNavigation implements IMobileNavigation {
    // Элемент меню
    private readonly menu: Element;
    // Кнопки перехода вперед - на уровень глубже
    private forward: HTMLElement[];
    // Кнопки перехода назад - на уровень выше
    private back: HTMLElement[];
    // Объект для наполнения элементами вложенных меню
    private menuObject: IMobileNavigationMenu = {};
    // Массив всех вложенных меню
    private allMenu: HTMLElement[];

    constructor(options: IMobileNavigationOptions) {
        this.menu = options.element || document.querySelector('.j-navigation-mobile');
        this.forward = [...this.menu.querySelectorAll('.j-navigation-mobile__menu-forward')] as HTMLElement[];
        this.back = [...this.menu.querySelectorAll('.j-navigation-mobile__menu-back')] as HTMLElement[];
        this.allMenu = [...this.menu.querySelectorAll(SELECTOR_MOBILE)] as HTMLElement[];
    }

    public init(): void {
        if (!this.allMenu.length) {
            return;
        }

        this._setElementsObject();
        this._subscribes();
        this._bindEvents();
    }

    // Наполняем объект меню и параллельно присваиваем кнопкам вперед и назад ИД подчиняемых им меню в объекте
    private _setElementsObject(): void {
        this.forward.forEach((button: HTMLElement, index: number) => {
            const parent = button.closest(SELECTOR_MOBILE);

            // Если у текущей кнопки есть родитель с селектором указывающим на то что там может быть вложенное меню, то находим в нем это вложенное меню, также находим кнопку Назад в этом меню. Элемент меню помещаем в объект, а кнопкам присваиваем имя(индекс) в объекте у текущего меню.
            if (parent) {
                const menu = parent.querySelector(SELECTOR_MOBILE) as HTMLElement;
                const backButton = menu?.querySelector('.j-navigation-mobile__menu-back') as HTMLElement;

                button.dataset['menu'] = `${index}`;

                if (backButton) {
                    backButton.dataset['menu'] = `${index}`;
                }

                this.menuObject[index] = {
                    menu
                };
            }
        });
    }

    private _subscribes(): void {
        // Подписка на событие закрытия мобильного меню - для закрытия всех внутренних переходов
        observer.subscribe('closeMenuTrigger', () => {
            this.allMenu.forEach((item: HTMLElement) => {
                item.classList.remove(CLASS_ACTIVE);
            });
        });
    }

    private _bindEvents(): void {
        // Навешиваем событие клика на все кнопки "вперед". По нажатию добавляем активный класс дочернему меню относительно этой кнопки
        this.forward.forEach((button: HTMLElement) => {
            button.addEventListener('click', () => {
                // @ts-ignore
                this.menuObject[button.dataset['menu']]['menu'].classList.add(CLASS_ACTIVE);
            });
        });
        // Навешиваем событие клика на все кнопки "назад". По нажатию удаляем активный класс родительского меню относительно этой кнопки
        this.back.forEach((button: HTMLElement) => {
            button.addEventListener('click', () => {
                // @ts-ignore
                this.menuObject[button.dataset['menu']]['menu'].classList.remove(CLASS_ACTIVE);
            });
        });
    }
}

export default MobileNavigation;
