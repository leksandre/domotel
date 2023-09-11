import type {IScrollAnimation, IScrollAnimationOptions} from './types';

class scrollAnimation implements IScrollAnimation {
    private readonly targetY: number;
    private readonly duration: number;
    private start: number;
    private readonly startY: number;
    private animationName: number;

    constructor(options: IScrollAnimationOptions) {
        this.targetY = options.targetY ?? 0;
        this.duration = options.duration || 600;
        this.start = 0;
        this.startY = window.pageYOffset;
    }

    public scroll(targetCoord?: number | undefined, offset: number = 0): void {
        const targetY = targetCoord || this.targetY;
        const diff = targetY - this.startY - offset;

        const step = (timestamp: number): void => {
            if (!this.start) {
                this.start = timestamp;
            }

            const time = timestamp - this.start;
            const percent = Math.min(time / this.duration, 1);

            window.scrollTo(0, this.startY + (diff * percent));

            if (time < this.duration) {
                this.animationName = window.requestAnimationFrame(step.bind(this));
            }
        };

        this.animationName = window.requestAnimationFrame(step.bind(this));
    }
}

export default scrollAnimation;
