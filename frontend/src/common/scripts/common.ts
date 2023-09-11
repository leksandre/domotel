import type * as ClassTypes from './types/class';
import type {IAnimationOptions} from '@/components/animation/types';
import type {IDynamicImport} from './types/utils';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IValidation} from '@/components/form/validation/types';
import Observer from './observer';
import popupContentTemplate from '@/components/form/form-parametric-content.twig';
import {PostMessagePopup} from '@/components/popup/popup';
import {RESIZE_EVENTS} from '@/common/scripts/constants';
import {Utils} from './utils';
import Validation from '@/components/form/validation';
import type {YouTubePlayer} from 'youtube-player/dist/types';

const validation: IValidation = new Validation();
const observer: IObserver = new Observer();

// Поведение шапки
export const initHeader = (headerWrap: HTMLElement): void => {
    import(/* webpackChunkName: "header" */ '@/components/header')
        .then(({default: Header}: IDynamicImport) => {
            const targetScroll: HTMLElement[] = [...document.querySelectorAll('.j-main-screen')] as HTMLElement[];
            const header: ClassTypes.IHeader = new Header();

            header.init({
                headerWrap,
                targetScroll
            });
        });
};

// Поведение меню
export const initMobileMenu = (burgerMenuSelector: HTMLElement, headerWrap: HTMLElement): void => {
    import(/* webpackChunkName: "btn-trigger" */ '@/components/button-trigger')
        .then(({default: ButtonTrigger}: IDynamicImport) => {
            const menuTrigger: ClassTypes.IButtonTrigger = new ButtonTrigger();
            const mobileMenu = headerWrap.querySelector('.j-navigation-mobile') as HTMLElement;
            const openMenuClass = 'is-open-menu';

            menuTrigger.init({
                target    : burgerMenuSelector,
                eventOpen : 'showMenuTrigger',
                eventClose: 'closeMenuTrigger'
            });

            observer.subscribe('showMenuTrigger', () => {
                headerWrap.classList.add(openMenuClass);
                Utils.bodyFixed(mobileMenu);
            });

            observer.subscribe('closeMenuTrigger', () => {
                headerWrap.classList.remove(openMenuClass);
                Utils.bodyStatic();
            });

            observer.subscribe('scrollBy:start', () => {
                headerWrap.classList.remove(openMenuClass);
                Utils.bodyStatic();
            });

            RESIZE_EVENTS.forEach((event: string) => {
                window.addEventListener(event, Utils.throttle(() => {
                    menuTrigger.closeByResize();
                }, 250));
            });
        });
};

/* Подключение ленивой загрузки (img/picture/iframe) */
export const lazyLoadElements = (lazyElements: Element[]): void => {
    const replaceSource = (element: Element): void => {
        const parentElement = element.parentElement;

        if (!parentElement) {
            return;
        }

        if (parentElement.tagName === 'PICTURE' && element.tagName === 'IMG') {
            element.addEventListener('load', () => {
                parentElement.style.backgroundImage = '';
            });
        }

        const targetAttribute = element.nodeName === 'SOURCE' ? 'srcset' : 'src';
        const sourceElement = element.nodeName === 'SOURCE' ? 'data-srcset' : 'data-src';

        if (element.getAttribute(sourceElement) !== null) {
            element.setAttribute(targetAttribute, element.getAttribute(sourceElement) || '');
            element.removeAttribute(sourceElement);
        }
    };

    if ('IntersectionObserver' in window) {
        // Иначе по событию IntersectionObserver
        const lazyImageObserver = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
            entries.forEach((entry: IntersectionObserverEntry) => {
                if (entry.isIntersecting) {
                    const lazyEl = entry.target;

                    replaceSource(lazyEl);
                    lazyImageObserver.unobserve(lazyEl);
                }
            });
        });

        lazyElements.forEach((lazyImage: Element) => {
            if (Utils.isInViewport(lazyImage as HTMLElement,
                window.innerHeight || document.documentElement.clientHeight)) {
                replaceSource(lazyImage);
            } else {
                lazyImageObserver.observe(lazyImage);
            }
        });
    } else {
        // Если есть нативная поддержка атрибута или нет поддержки IntersectionObserver - подменяем сразу
        lazyElements.forEach((lazyEl: Element) => {
            replaceSource(lazyEl);
        });
    }
};

export const initPopup = (buttons: HTMLElement[], otherOptions: Partial<ClassTypes.IPopupFacadeOptions> = {}): void => {
    import(/* webpackChunkName: "popup-facade" */ '@/components/popup/popupFacade')
        .then(({default: PopupFacade}: IDynamicImport) => {
            const options: ClassTypes.IPopupFacadeOptions = {
                ...{openButtons: buttons},
                ...otherOptions
            };

            const t: ClassTypes.IPopupFacade = new PopupFacade(options);

            t.init();
        });
};

export const initVideoTabs = (wrapperSelector: string): void => {
    const wrapper = document.querySelector(wrapperSelector);

    if (wrapper) {
        const tabs: HTMLElement[] = Array.from(wrapper.querySelectorAll('.j-video__tab'));
        const iframe = wrapper.querySelector('.j-video__iframe') as HTMLIFrameElement;
        const isActive = 'is-active';

        tabs.forEach((tab: HTMLElement): void => {
            tab.addEventListener('click', (event: MouseEvent) => {
                const target = event.currentTarget as HTMLElement;

                if (!target || !iframe) {
                    return;
                }

                iframe.src = target.dataset['video'] || '';
                tabs.forEach((element: HTMLElement) => {
                    const method = element === target ? 'add' : 'remove';

                    element.classList[method](isActive);
                });
            });
        });
    }
};

// Установка размеров видео первого экрана
export const initFullVideo = (fullIframe: HTMLElement): void => {
    const wrapper = document.querySelector('.j-first-screen__video') as HTMLElement;
    const ratio = 0.5625;

    if (!wrapper) {
        return;
    }

    const setVideoSize = (): void => {
        const width = Utils.getWindowWidth();
        const height = Utils.isMobile() ? wrapper.offsetHeight : Utils.getWindowHeight();
        const dimension = height / width;

        if (dimension > ratio) {
            fullIframe.style.width = `${height / ratio}px`;
            fullIframe.style.height = `${height + Math.abs(width - (height / ratio))}px`;
            fullIframe.style.top = `${0.5 * (width - (height / ratio))}px`;
            fullIframe.style.left = `${0.5 * (width - (height / ratio))}px`;
        } else {
            fullIframe.style.width = `${width}px`;
            fullIframe.style.height = `${(width * ratio) + (height * ratio)}px`;
            fullIframe.style.top = `${-Math.abs(0.5 * (height * ratio))}px`;
            fullIframe.style.left = '0';
        }
    };

    RESIZE_EVENTS.forEach((event: string) => {
        window.addEventListener(event, setVideoSize);
    });

    setVideoSize();
};

export const initYoutubePlayer = (wrapper: HTMLElement): void => {
    const firstScriptTag = document.getElementsByTagName('script')[0];
    const firstScriptParentTag = firstScriptTag?.parentNode;

    if (!firstScriptParentTag) {
        return;
    }

    const tag = document.createElement('script');
    let player: YouTubePlayer | null = null;

    tag.src = 'https://www.youtube.com/iframe_api';
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    window.onYouTubeIframeAPIReady = () => {
        player = new window.YT.Player(wrapper, {
            videoId   : `${wrapper.dataset['id']}`,
            playerVars: {
                modestbranding: 1,
                autoplay      : 1,
                mute          : 1,
                controls      : 0,
                showinfo      : 0,
                wmode         : 'transparent',
                branding      : 0,
                rel           : 0,
                autohide      : 1,
                origin        : window.location.origin
            },
            events: {
                onReady: onPlayerReady,
                onStateChange(event: CustomEvent & {data: number}) {
                    if (!event.data) {
                        player?.playVideo();
                    }
                }
            }
        });

        // eslint-disable-next-line func-style
        function onPlayerReady(event: CustomEvent): void {
            // повторный initFullVideo() уже после инита плеера + ресайз
            const youtubeIframe = document.querySelector('.j-first-screen__youtube') as HTMLElement;

            initFullVideo(youtubeIframe);
            if (!event.target) {
                return;
            }

            (event.target as unknown as YouTubePlayer).playVideo();

            RESIZE_EVENTS.forEach((resizeEvent: string) => {
                window.addEventListener(resizeEvent, () => {
                    initFullVideo(youtubeIframe);
                });
            });
        }
    };
};

// Инициализация форм
export const initForm = (wrapper: HTMLElement): void => {
    import(/* webpackChunkName: "form" */ '@/components/form')
        .then(({default: Form}: IDynamicImport) => {
            new Form().init({
                target        : wrapper,
                successMessage: true
            });
        });
};

// Инициализация формы обратной связи в попапах
export const initCallbackForm = (): void => {
    observer.subscribe('popup:open', (popup: ClassTypes.IPopup) => {
        if (!popup.content) {
            return;
        }

        // Форма
        const callbackFormElement = popup.content.querySelector('.j-form') as HTMLElement;

        if (callbackFormElement) {
            validation.update();
            import(/* webpackChunkName: "form" */ '@/components/form')
                .then(({default: Form}: IDynamicImport) => {
                    new Form().init({
                        target        : callbackFormElement,
                        popupInstance : popup,
                        successMessage: true
                    });
                });
        }
    });
};

// Инициализация попапов с формой обратной связи
export const initCallbackPopup = (buttons: HTMLElement[]): void => {
    const options = {
        removeDelay         : 300,
        closeButtonAriaLabel: 'Закрыть форму обратного звонка',
        modify              : 'popup_theme_callback'
    };

    initCallbackForm();
    initPopup(buttons, options);
};

// Фиксирует высоту блоков
export const initMatchHeight = (element: HTMLElement): void => {
    const childElements: HTMLElement[] = Array.from(element.querySelectorAll('.j-match-height__child'));
    const heights: number[] = [];

    const getHeights = (): void => {
        heights.length = 0;
        childElements.forEach((childElement: HTMLElement): void => {
            heights.push(childElement.offsetHeight);
        });
    };

    const getHighest = (): number => {
        return Math.max(...heights);
    };

    const setHeight = (): void => {
        getHeights();
        const tallest = getHighest();

        childElements.forEach((childElement: HTMLElement) => {
            childElement.style.height = `${tallest}px`;
        });
    };

    setHeight();

    RESIZE_EVENTS.forEach((event: string) => {
        window.addEventListener(event, () => {
            childElements.forEach((childElement: HTMLElement) => {
                childElement.style.removeProperty('height');
            });
            setHeight();
        });
    });
};

// Табы
export const initTabs = (elem: HTMLElement): void => {
    import(/* webpackChunkName: "tabs" */ '@/components/tabs')
        .then(({default: Tabs}: IDynamicImport) => {
            new Tabs().init({elem});
        });
};

// Аккордеон
export const initAccordions = (element: Element): void => {
    import(/* webpackChunkName: "accordion" */ '@/components/accordion')
        .then(({default: Accordion}: IDynamicImport) => {
            const t = new Accordion();

            t.init({
                element,
                closeTimeout: true,
                selectors   : {
                    toggler     : '.j-accordion-header',
                    content     : '.j-accordion-content',
                    contentOuter: '.j-accordion-content-outer',
                    title       : '.j-accordion-title',
                    blockElement: '.j-flats-block-desktop'
                }
            });
        });
};

// Шаг назад
export const stepBack = (back: HTMLElement): void => {
    back.addEventListener('click', (event: MouseEvent) => {
        event.preventDefault();

        if (history.length === 1) {
            window.location.href = window.location.origin;
        } else {
            history.back();
        }
    });
};

// Шеринг
export const initShare = (shareElements: HTMLElement[]): void => {
    import(/* webpackChunkName: "share" */ '@/components/share')
        .then(({default: Share}: IDynamicImport) => {
            shareElements.forEach((element: HTMLElement) => {
                new Share().init({
                    target: element
                });
            });
        });
};

// НОВАЯ Анимация блоков при прокрутке (GSAP)
export const initAnimation = (options: Partial<IAnimationOptions> = {blockSelector: '.j-animation__section'}): void => {
    const animationWrapper = document.querySelector('.j-animation');

    if (animationWrapper) {
        import(/* webpackChunkName: "animation" */ '@/components/animation')
            .then(({default: Animation}: IDynamicImport) => {
                return new Animation({
                    wrapper: animationWrapper,
                    ...options
                });
            });
    }
};

export const initPostMessagePopup = (): void => {
    return new PostMessagePopup({
        closeButtonAriaLabel: 'Закрыть'
    }).init();
};

export const insertPremisesData = (wrapper: HTMLElement, data: ClassTypes.IPopupIframeData): void => {
    const currentForm = wrapper.querySelector('.j-form') as HTMLElement;
    const container = currentForm?.querySelector('.j-form__parametric-content') as HTMLElement;

    if (!currentForm || !container) {
        return;
    }

    const fillField = currentForm.querySelector('.j-form__fill-field') as HTMLInputElement;

    if (fillField) {
        fillField.value = `${data.description}`;
    }

    Utils.clearHtml(container);
    Utils.insetContent(container, popupContentTemplate(data));
};

export const initPlanoplanWidget = (uid: string): void => {
    const primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--color-brand-base') || null;

    window.Planoplan.init({
        uid,
        width          : '100%',
        height         : '100%',
        el             : 'planoplan',
        textColor      : '#000000',
        backgroundColor: '#ffffff',
        ...primaryColor && {
            primaryColor
        }
    });
};

export const setCompilationHeight = (card: HTMLElement): void => {
    const element: HTMLElement = card.closest('.j-main-screen') as HTMLElement || document.documentElement;

    const setSize = (): void => {
        element.style.setProperty('--fs-ch', `-${card.offsetHeight / 2}px`);
    };

    RESIZE_EVENTS.forEach((event: string): void => {
        window.addEventListener(event, Utils.throttle(() => {
            setSize();
        }, 100));
    });

    setSize();
};

export const initFirstScreenModifier = (item: HTMLElement, header: HTMLElement): void => {
    const parentItem: ParentNode | null = item.parentNode;

    if (!parentItem) {
        return;
    }
    const itemIndex: number = [...parentItem.children].indexOf(item);
    const version: string | undefined = item.dataset['version'];

    if (itemIndex !== 0 || !version) {
        return;
    }

    header.classList.add(`header_main_screen-v${item.dataset['version']}`);
};
