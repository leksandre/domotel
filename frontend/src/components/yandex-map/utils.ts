export default {

    /**
     * Проверяет нужно ли показывать панель вместо баллуна;
     * @return {boolean} true/false - true - нужно/false нет
     */
    needBalloonPanel() {
        const panelBreakpoint = 960;

        return window.innerWidth < panelBreakpoint;
    },

    /**
     * Проверяет мобилка ли сейчас или нет
     * @return {boolean} true/false - мобилка/десктоп
     */
    isMobile() {
        const desktopBreakpoint = 1280;

        return window.innerWidth < desktopBreakpoint;
    }
};

