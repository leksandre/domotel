<template>
    <svg :class="className" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 8L21 8" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M21 16L3 16" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="8" cy="8" r="2" fill="black" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="16" cy="16" r="2" transform="rotate(-180 16 16)" fill="black" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>

<script>
export default {
    name: 'FilterIcon',

    props: ['className']
}
</script>

