import type {
    IAnimationItem,
    IAnimationObject,
    IAnimationOptions,
    IAnimationRow,
    IAnimationRowItem,
    IAnimationRowSetting
} from './types';
import {gsap} from 'gsap';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {Utils} from '@/common/scripts/utils';

gsap.registerPlugin(ScrollTrigger);

const observer: IObserver = new Observer();
const breakpointsNames: string[] = ['desktop', 'tabletL', 'tabletP', 'mobile'];
const breakpoints = {
    mobile : 320,
    tabletP: 670,
    tabletL: 960,
    desktop: 1280
};

class Animation {
    private wrapper: HTMLElement;
    private readonly blockSelector: string;
    private readonly rowSelector: string;
    private animationObjects: IAnimationObject[];

    private body: HTMLBodyElement | null = document.querySelector('body');
    private customRowSettings: string[] = ['320:1', '670:2', '960:3'];
    private documentHeight: number = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);

    constructor(options: IAnimationOptions) {
        this.wrapper = options.wrapper;
        this.blockSelector = options?.blockSelector || '.j-animation__section';
        this.rowSelector = options?.rowSelector || '.j-animation__row';
        this.animationObjects = [...this.wrapper.querySelectorAll(this.blockSelector)]
            .map((animationBlock: Element) => {
                return {
                    animationBlock,
                    items: [...animationBlock.querySelectorAll(`
                            .j-animation__header,
                            .j-animation__item,
                            .j-animation__content p,
                            .j-animation__content li,
                            .j-animation__content .j-animation__content-item`)] as IAnimationItem[],
                    rows: [...animationBlock.querySelectorAll(this.rowSelector)] as IAnimationRow[]
                };
            });

        this._init();
        this._subscribes();
        this._bindEvents();
    }

    private _init(): void {
        this.animationObjects.forEach((animationObject: IAnimationObject) => {
            this._animateContent(animationObject);
            this._animateRows(animationObject);
        });
    }

    private _subscribes(): void {
        observer.subscribe('accordion:open', () => {
            ScrollTrigger.refresh(true);
        });

        observer.subscribe('details:open', () => {
            ScrollTrigger.refresh(true);
        });

        observer.subscribe('accordion:close', () => {
            ScrollTrigger.refresh(true);
        });

        observer.subscribe('details:close', () => {
            ScrollTrigger.refresh(true);
        });
    }

    private _bindEvents(): void {
        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, Utils.throttle(() => {
                this.documentHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
            }, 250));
        });

        if (this.body) {
            this.body.addEventListener('updateAnimation', () => {
                ScrollTrigger.refresh(true);
            });
        }
    }

    private _getKeyByValue(object: Record<string, number>, value: number): string | undefined {
        return Object.keys(object).find((key: string) => {
            return object[key] === value;
        });
    }

    /**
     * Анимация основных элементов страницы
     * @param {object} animationObject - объект анимации с нодами
     * @private
     */
    private _animateContent(animationObject: IAnimationObject): void {
        animationObject.items.forEach((item: IAnimationItem) => {
            const elemTop = item.getBoundingClientRect().top + window.scrollY;
            const topPosition = Math.ceil((elemTop + 40) * 100 / this.documentHeight);
            const start = topPosition > 55 ? 'top 100%' : 'top 90%+=40px';
            const end = topPosition > 55 ? `top ${topPosition}%+=20px` : 'top 50%+=40px';

            item.timeline = gsap.timeline({
                scrollTrigger: {
                    trigger: item,
                    start,
                    end,
                    scrub  : 1
                    // для отладки анимаций
                    // markers: true
                }
            });

            item.timeline.fromTo(item, {
                opacity   : 0,
                translateY: '40px'
            }, {
                opacity   : 1,
                translateY: '0'
            }, 0);
        });
    }

    /**
     * Анимация последовательных элементов (карточки в ряд)
     * @param {object} animationObject - объект анимации с нодами
     * @private
     */
    private _animateRows(animationObject: IAnimationObject): void {
        this._setRowsSettings(animationObject);
        this._setRowsAnimation(animationObject);
    }

    private _setRowsSettings(animationObject: IAnimationObject): void {
        animationObject.rows.forEach((row: IAnimationRow) => {
            row.items = [...row.querySelectorAll('.j-animation__row-item')] as IAnimationRowItem[];
            if (!row.items.length) {
                return;
            }

            const settingsData = row.dataset['items'] || '';
            let settingsArray = settingsData.split(',');

            // @ts-ignore
            if (settingsArray.length === 1 && settingsArray[0].indexOf(':') === -1) {
                settingsArray = [...this.customRowSettings];
            }

            row.settings = settingsArray.reduce((accumulator: IAnimationRowSetting[], currentValue: string) => {
                const setting = currentValue.split(':');

                if (setting.length !== 2 || isNaN(Number(setting[0])) || isNaN(Number(setting[1]))) {
                    return accumulator;
                }

                accumulator.push({
                    name      : this._getKeyByValue(breakpoints, Number(setting[0])) || 'wrongBreakpoint',
                    breakpoint: Number(setting[0]),
                    itemCounts: Number(setting[1])
                });

                return accumulator;
            }, []);
        });
    }

    private _setRowsAnimation(animationObject: IAnimationObject): void {
        animationObject.rows.forEach((row: IAnimationRow) => {
            row.settings.forEach((setting: IAnimationRowSetting) => {
                if (window.matchMedia(`(min-width: ${setting.breakpoint}px)`).matches) {
                    row.currentSetting = setting;
                }
            });

            row.items.forEach((item: IAnimationRowItem) => {
                item.mm = gsap.matchMedia();
                item.mm.add({
                    mobile : `(min-width: ${breakpoints.mobile}px)`,
                    tabletP: `(min-width: ${breakpoints.tabletP}px)`,
                    tabletL: `(min-width: ${breakpoints.tabletL}px)`,
                    desktop: `(min-width: ${breakpoints.desktop}px)`
                }, (context: gsap.Context) => {
                    let itemCounts: number | null = null;

                    for (let i = 0; i < breakpointsNames.length; i++) {
                        // @ts-ignore
                        if (context.conditions?.[breakpointsNames[i]]) {
                            itemCounts = row.settings.find((setting: IAnimationRowSetting) => {
                                return setting.name === breakpointsNames[i];
                            })?.itemCounts as number;

                            if (itemCounts) {
                                break;
                            }
                        }
                    }
                    if (itemCounts) {
                        item.timeline = gsap.timeline({
                            delay        : 1,
                            scrollTrigger: {
                                trigger: item,
                                start  : `top 90%+=${40 - (row.items.indexOf(item) % itemCounts * 60)}px`,
                                end    : `top 50%+=${40 - (row.items.indexOf(item) % itemCounts * 60)}px`,
                                scrub  : 1
                                // для отладки анимаций
                                // markers: true
                            }
                        });

                        item.timeline.fromTo(item, {
                            opacity   : 0,
                            translateY: `${40 + (20 * itemCounts)}px`
                        }, {
                            opacity   : 1,
                            translateY: '0'
                        }, 0);
                    }
                });
            });
        });
    }
}

export default Animation;
