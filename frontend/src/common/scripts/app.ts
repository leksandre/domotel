import type * as ClassTypes from './types/class';
import {
    initAccordions,
    initAnimation,
    initCallbackPopup,
    initFirstScreenModifier,
    initForm,
    initFullVideo,
    initHeader,
    initMatchHeight,
    initMobileMenu,
    initPopup,
    initPostMessagePopup,
    initShare,
    initTabs,
    initYoutubePlayer,
    lazyLoadElements,
    setCompilationHeight,
    stepBack
} from './common';
import {EElementPosition} from '@/components/slider/types';
import type {IDynamicImport} from './types/utils';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from './observer';

const observer: IObserver = new Observer();

// Поведение шапки
const headerWrap = document.querySelector('.j-header') as HTMLElement;

if (headerWrap) {
    initHeader(headerWrap);
}

// Поведение меню
const burgerMenuSelector = document.querySelector('.j-burger') as HTMLElement;
const headerWrapper = headerWrap || document.querySelector('header') as HTMLElement;

if (burgerMenuSelector && headerWrapper) {
    initMobileMenu(burgerMenuSelector, headerWrapper);
}

// Модификатор хэдера для первого экрана
const firstScreens = [...document.querySelectorAll('.j-main-screen')] as HTMLElement[];

firstScreens.forEach((firstScreen: HTMLElement): void => {
    if (headerWrapper) {
        initFirstScreenModifier(firstScreen, headerWrapper);
    }
});

// Cкролл по якорям
const anchors = [...document.querySelectorAll('.j-anchor')] as HTMLElement[];

if (anchors.length) {
    import(/* webpackChunkName: "scrollBy" */ '@/components/scrollBy')
        .then(({default: ScrollBy}: IDynamicImport) => {
            const scrollBy: ClassTypes.IScrollBy = new ScrollBy();
            const CLASS_ACTIVE = 'is-active';

            scrollBy.init({
                takeElements: [headerWrap],
                anchors
            });

            const setActiveAnchor = (id: string): void => {
                anchors.forEach((item: HTMLElement) => {
                    const href = item.getAttribute('href');

                    item.classList.remove(CLASS_ACTIVE);

                    if (href === id || href === `/${id}`) {
                        item.classList.add(CLASS_ACTIVE);
                    }
                });
            };

            const hash = window.location.hash;

            if (hash.length) {
                setActiveAnchor(hash);
            }

            observer.subscribe('anchorChangeSection', (id: string) => {
                setActiveAnchor(id);
            });
        });
}

// Слайдер на первом экране
const sliderWrap = document.querySelector('.j-first-screen-slider') as HTMLElement;

if (sliderWrap) {
    import(/* webpackChunkName: "first-screen-section" */ '@/sections/first-screen')
        .then(({default: initFirstScreenSlider}: IDynamicImport) => {
            initFirstScreenSlider(sliderWrap);
        });
}

// Видео на первом экране
const fullIframes = [...document.querySelectorAll('.j-first-screen__iframe')] as HTMLElement[];

fullIframes.forEach((fullIframe: HTMLElement) => {
    initFullVideo(fullIframe);
});

// Видео youtube на первом экране (инициализация плеера через API youtube)
const youtubeFrame = document.querySelector('.j-first-screen__youtube') as HTMLElement;

if (youtubeFrame) {
    initFullVideo(youtubeFrame);
    initYoutubePlayer(youtubeFrame);
}

// Скролл на высоту главного экрана
const firstScreenScrollElement = document.querySelector('.j-first-screen-scroll');

if (firstScreenScrollElement) {
    import(/* webpackChunkName: "first-screen-scroll" */ '@/components/first-screen-scroll')
        .then(({default: FirstScreenScroll}: IDynamicImport) => {
            const scroll: ClassTypes.IFirstScreenScroll = new FirstScreenScroll({
                element        : firstScreenScrollElement,
                wrapperSelector: '.j-main-screen'
            });

            scroll.init();
        });
}

// Большой слайдер
const bigSliderWrappers = [...document.querySelectorAll('.j-slider')] as HTMLElement[];

if (bigSliderWrappers.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            bigSliderWrappers.forEach((bigSliderWrapper: HTMLElement) => {
                const slider: ClassTypes.ISlider = new Slider();

                slider.init({
                    slider       : bigSliderWrapper,
                    modify       : 'big_slide',
                    arrow        : true,
                    arrowPosition: EElementPosition.caption,
                    counter      : true,
                    isCaption    : true,
                    infinityLoop : true
                });
            });
        });
}

// Подключение мелких галерей
const slidersMini = [...document.querySelectorAll('.j-slider-mini')] as HTMLElement[];

if (slidersMini.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            slidersMini.forEach((item: HTMLElement) => {
                const sliderMini: ClassTypes.ISlider = new Slider();

                sliderMini.init({
                    slider       : item,
                    isCaption    : true,
                    arrow        : true,
                    arrowPosition: EElementPosition.caption,
                    counter      : true,
                    modify       : 'mini_slide',
                    infinityLoop : true
                });
            });
        });
}

const slidersUtp = [...document.querySelectorAll('.j-slider-utp')] as HTMLElement[];

if (slidersUtp.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            slidersUtp.forEach((item: HTMLElement) => {
                const sliderUtp: ClassTypes.ISlider = new Slider();

                sliderUtp.init({
                    slider       : item,
                    arrow        : true,
                    arrowPosition: EElementPosition.allSlider,
                    modify       : 'utp_slide',
                    // todo???
                    infinityLoop : true,
                    slideVisible : 1.15,
                    responsive   : {
                        320 : 1.15,
                        670 : 2.25,
                        1280: 3
                    }
                });
            });
        });
}

// Галерея подборок квартир на главной
const slidersCompilation = [...document.querySelectorAll('.j-flats-compilation-slider')] as HTMLElement[];

if (slidersCompilation.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            slidersCompilation.forEach((item: HTMLElement) => {
                const sliderCompilation: ClassTypes.ISlider = new Slider();

                sliderCompilation.init({
                    slider      : item,
                    arrow       : true,
                    slideVisible: 4,
                    slideCount  : 3,
                    responsive  : {
                        670 : 3,
                        1280: 4
                    }
                });
            });
        });
}

// Подключение галерей новостей
const slidersNews = [...document.querySelectorAll('.j-slider-news')] as HTMLElement[];

if (slidersNews.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            slidersNews.forEach((item: HTMLElement) => {
                const sliderNews: ClassTypes.ISlider = new Slider();

                sliderNews.init({
                    slider       : item,
                    arrow        : true,
                    arrowPosition: EElementPosition.caption,
                    slideCount   : 3,
                    slideVisible : 3,
                    innerModify  : 'j-animation__slider-row',
                    innerData    : 'data-items="320:3"',
                    responsive   : {
                        0   : 1 + (5 / 31),
                        670 : 2 + (5 / 15.5),
                        1280: 3
                    }
                });

                initAnimation({
                    blockSelector: '.j-animation__slider',
                    rowSelector  : '.j-animation__slider-row'
                });
            });
        });
}

// Галерея планировок в карточке квартиры
const flatSliderWrappers = [...document.querySelectorAll('.j-slider-flat')] as HTMLElement[];

if (flatSliderWrappers.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            flatSliderWrappers.forEach((flatSliderWrapper: HTMLElement) => {
                const slider: ClassTypes.ISlider = new Slider();

                slider.init({
                    slider       : flatSliderWrapper,
                    arrow        : true,
                    arrowPosition: EElementPosition.caption,
                    counter      : true
                });
            });
        });
}

// Подключение мелких галерей
const flatBlockSliderWrappers = [...document.querySelectorAll('.j-slider-flat-block')] as HTMLElement[];

if (flatBlockSliderWrappers.length) {
    import(/* webpackChunkName: "slider" */ '@/components/slider')
        .then(({default: Slider}: IDynamicImport) => {
            flatBlockSliderWrappers.forEach((item: HTMLElement) => {
                const flatBlockSlider: ClassTypes.ISlider = new Slider();

                flatBlockSlider.init({
                    slider      : item,
                    dots        : true,
                    infinityLoop: true
                });
            });
        });
}

/* Ленивая загрузка */
/* подключать после инициализации слайдеров */
const lazyElements = [...document.querySelectorAll('[loading=lazy]')];

if (lazyElements.length) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            lazyLoadElements(lazyElements);
        });
    } else {
        lazyLoadElements(lazyElements);
    }
}

observer.subscribe('slider:ready', (object: ClassTypes.ISlider) => {
    const sliderLazyElements = [...object.slider.querySelectorAll('[loading=lazy]')];

    lazyLoadElements(sliderLazyElements);
});

// Плавный подскролл по якорям
const simpleAnchors = [...document.querySelectorAll(`a[href^="#"]:not(.j-anchor)`)];

if (simpleAnchors.length) {
    simpleAnchors.forEach((anchor: Element) => {
        const anchorHref = anchor.getAttribute('href');

        if (!anchorHref || anchorHref?.length === 1) {
            return;
        }

        const anchorBlock = document.querySelector(anchorHref);

        if (anchorBlock) {
            (anchor as HTMLElement).addEventListener('click', (event: MouseEvent) => {
                event.preventDefault();

                anchorBlock.scrollIntoView({
                    behavior: 'smooth'
                });
            });
        }
    });
}

// Табы
const tabs = [...document.querySelectorAll('.j-tabs-wrap')];

tabs.forEach((elem: Element) => {
    initTabs(elem as HTMLElement);
});

// Аккордеон
const accordions = [...document.querySelectorAll('.j-accordion')];

if (accordions.length) {
    accordions.forEach((accordion: Element) => {
        initAccordions(accordion);
    });
}

// Details Accordion
const details = [...document.querySelectorAll('.j-details')];

if (details.length) {
    import(/* webpackChunkName: "details-accordion" */ '@/components/details')
        .then(({default: DetailsAccordion}: IDynamicImport) => {
            details.forEach((element: Element) => {
                const accordion: ClassTypes.IDetailsAccordion = new DetailsAccordion();

                accordion.init(element as HTMLDetailsElement);
            });
        });
}

// Прячущаяся навигация (меню без выборщиков)
const navigationWrappers = [...document.querySelectorAll('.j-header__nav')];

if (navigationWrappers.length) {
    import(/* webpackChunkName: "navigation" */ '@/components/navigation')
        .then(({default: Navigation}: IDynamicImport) => {
            navigationWrappers.forEach((navigationWrapper: Element) => {
                const navigation: ClassTypes.INavigation = new Navigation();

                navigation.init({
                    target           : navigationWrapper as HTMLElement,
                    wrapper          : '.j-header__inner',
                    driveElements    : '.j-header__drive',
                    foldingNavigation: '.j-folding-navigation',
                    breaks           : {
                        min: 960,
                        max: 1600
                    }
                });
            });
        });
}

// Навигация с группировкой (меню с выборщиками)
const navigationGroupWrappers = [...document.querySelectorAll('.j-group-navigation')];

navigationGroupWrappers.forEach((navigationGroupWrapper: Element) => {
    import(/* webpackChunkName: "navigation-group" */ '@/components/navigation-group')
        .then(({default: NavigationGroup}: IDynamicImport) => {
            const navigationGroup: ClassTypes.INavigationGroups = new NavigationGroup({
                navigation: navigationGroupWrapper
            });

            navigationGroup.init();
        });
});


// Формы
const formWrappers = [...document.querySelectorAll('.j-form')];

formWrappers.forEach((wrapper: Element) => {
    initForm(wrapper as HTMLElement);
});

// Попапы
const popupSelectors = [...document.querySelectorAll('.j-popup')] as HTMLElement[];

if (popupSelectors.length) {
    initPopup(popupSelectors, {removeDelay: 300});
}

// Отдельный селектор для попапов обратных звонков
const popupFacadeLinks = [...document.querySelectorAll('.j-popup-callback:not([data-init])')] as HTMLElement[];

if (popupFacadeLinks.length) {
    initCallbackPopup(popupFacadeLinks);

    popupFacadeLinks.forEach((button: HTMLElement) => {
        button.dataset['init'] = 'true';
    });
}

// Фиксирует высоту блоков
const matchHeightWrappers = [...document.querySelectorAll('.j-match-height')];

matchHeightWrappers.forEach((matchHeightWrapper: Element) => {
    initMatchHeight(matchHeightWrapper as HTMLElement);
});

// Шаг назад
const stepBackSelector = document.querySelector('.j-back') as HTMLElement;

if (stepBackSelector) {
    stepBack(stepBackSelector);
}

// Шеринг
const shareElements = [...document.querySelectorAll('.j-share')] as HTMLElement[];

if (shareElements.length) {
    initShare(shareElements);
}

// Ипотечный калькулятор
const mortgageCalculator = document.querySelector('.j-mortgage-calculator') as HTMLElement;

if (mortgageCalculator) {
    import(/* webpackChunkName: "mortgage-calculator" */ '@/components/mortgage-calculator')
        .then(({default: MortgageCalculator}: IDynamicImport) => {
            const calculator: ClassTypes.IMortgageCalculator = new MortgageCalculator(mortgageCalculator);

            calculator.init();

            // Отображения блока и подстановка месячного платежа ипотеки в карточке квартиры
            const priceMortgageWrappers = [...document.querySelectorAll('.j-price__mortgage')];
            const monthlyPayment = mortgageCalculator.dataset['minMonthly'];

            if (monthlyPayment?.length) {
                priceMortgageWrappers.forEach((wrapper: Element) => {
                    import(/* webpackChunkName: "price-monthly-payment" */ '@/components/price')
                        .then(({default: Price}: IDynamicImport) => {
                            const priceMonthlyPayment: ClassTypes.IPrice = new Price({
                                element: wrapper,
                                payment: monthlyPayment,
                                href   : mortgageCalculator.closest('section')?.id || null
                            });

                            priceMonthlyPayment.init();
                        });
                });
            }
        });
}

// Мобильное меню
const mobileNavigation = document.querySelector('.j-navigation-mobile');

if (mobileNavigation) {
    import(/* webpackChunkName: "mobile-navigation" */ '@/components/navigation-mobile')
        .then(({default: MobileNavigation}: IDynamicImport) => {
            const navigation = new MobileNavigation({
                element: mobileNavigation
            });

            navigation.init();
        });
}

// Анимация
initAnimation();

// j-visual-frame
const visualIframes = [...document.querySelectorAll('.j-visual-frame')];

if (visualIframes.length) {
    initPostMessagePopup();
}

// Уведомление об использовании cookies
const cookiesWrapper = document.querySelector('.j-cookies-notice') as HTMLElement;

if (cookiesWrapper) {
    import(/* webpackChunkName: "cookies-notice" */ '@/components/cookies-notice')
        .then(({default: CookiesNotice}: IDynamicImport) => {
            const cookiesNotice: ClassTypes.ICookiesNotice = new CookiesNotice();

            cookiesNotice.init(cookiesWrapper);
        });
}

const location = document.querySelector('.j-location-map') as HTMLElement;

if (location) {
    import(/* webpackChunkName: "location-section" */ '@/sections/location')
        .then(({default: initLocation}: IDynamicImport) => {
            initLocation(location);
        });
}

// карта в секции контактов
const contactsMap = document.querySelector('.j-contacts-map') as HTMLElement;

if (contactsMap) {
    import(/* webpackChunkName: "contacts-section" */ '@/sections/contacts')
        .then(({default: initContactsMap}: IDynamicImport) => {
            initContactsMap(contactsMap);
        });
}

// Инициализация планоплана внутри табов
const planoplanElements = [...document.querySelectorAll('.j-flat__planoplan-widget')] as HTMLElement[];

if (planoplanElements.length) {
    import(/* webpackChunkName: "flat-block" */ '@/components/flat')
        .then(({default: initPlanoplanWidgets}: IDynamicImport) => {
            initPlanoplanWidgets(planoplanElements);
        });
}

const firstScreenCards = [...document.querySelectorAll('.j-first-screen-offset')] as HTMLElement[];

firstScreenCards.forEach((card: HTMLElement): void => {
    setCompilationHeight(card);
});
