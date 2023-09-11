import {EElementPosition} from '@/components/slider/types';
import type {ISlider} from '@/components/slider/types';
import Slider from '@/components/slider';

// Большой слайдер
const initFirstScreenSlider = (sliderWrap: HTMLElement): void => {
    const slider: ISlider = new Slider();

    slider.init({
        slider       : sliderWrap,
        dots         : false,
        arrow        : false,
        counter      : false,
        arrowPosition: EElementPosition.caption,
        noSwipe      : true,
        autoplay     : true,
        autoplaySpeed: 5000,
        infinityLoop : true
    });
};

export default initFirstScreenSlider;
