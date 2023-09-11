<template>
    <svg :class="className" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 1L10.8885 7.11146H17L12.0557 10.8885L13.9443 17L9 13.2229L4.05573 17L5.94427 10.8885L1 7.11146H7.11146L9 1Z"
              stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>

<script>
export default {
    name: 'PremisesFavouriteIcon',

    props: ['className']
}
</script>

<style lang="scss">
@import "Icons";
</style>
