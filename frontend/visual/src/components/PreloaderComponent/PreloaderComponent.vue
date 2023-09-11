<template>
    <transition name="fade">
        <div class="visual-loader loader">
            <div class="loader__item"></div>
            <div class="loader__item"></div>
            <div class="loader__item"></div>
        </div>
    </transition>
</template>

<script>

export default {
    name: 'PreloaderComponent'
};
</script>

<style lang="scss">
@import 'PreloaderComponent';
@import "../../../../src/components/loader/loader";
</style>
