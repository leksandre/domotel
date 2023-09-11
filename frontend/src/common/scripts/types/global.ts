export {};

declare global {
    interface Window {
        DocumentTouch: any;
        Planoplan: any;
        yandexMapKey: string;
        ymaps: any;
        YT: any;
        // глобальный флаг для состояния попапа с хэшом
        isSPWHOpen: boolean;
        onYouTubeIframeAPIReady?(): void;
    }
}
