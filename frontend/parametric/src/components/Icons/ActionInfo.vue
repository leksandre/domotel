<template>
    <svg :class="className" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="7" cy="7" r="6" stroke="white"/>
        <path d="M7 10.375V7.375M7 5V4" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
</template>

<script>
export default {
    name: 'ActionInfo',

    props: ['className']
}
</script>

<style lang="scss">
@import "Icons";
</style>
