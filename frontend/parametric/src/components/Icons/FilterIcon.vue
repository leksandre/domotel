<template>
    <svg :class="className" width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0.5 3H18.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.5 11L0.5 11" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="5.5" cy="3" r="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="13.5" cy="11" r="2" transform="rotate(-180 13.5 11)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>

<script>
export default {
    name: 'FilterIcon',

    props: ['className']
}
</script>

<style lang="scss">
@import "Icons";
</style>
