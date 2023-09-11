export enum EButtonTriggerState {
    OPEN = 'open',
    CLOSE = 'close',
}

export interface IButtonTriggerOptions {
    target: HTMLElement;
    eventOpen?: string;
    eventClose?: string;
}

export interface IButtonTrigger {
    init(options: IButtonTriggerOptions): void;
    closeByResize(): void;
}
