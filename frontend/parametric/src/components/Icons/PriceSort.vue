<template>
    <svg :class="className" width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 1V9M3 9L1 7M3 9L5 7" stroke-linecap="round"/>
        <path d="M9 9L9 1M9 1L11 3M9 1L7 3" stroke-linecap="round"/>
    </svg>
</template>

<script>
export default {
    name: 'PriceSort',

    props: ['className']
}
</script>

<style lang="scss">
@import "Icons";
</style>
