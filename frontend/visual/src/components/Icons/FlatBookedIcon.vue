<template>
    <svg :class="className" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="12" cy="8.5" r="5" stroke-width="1.5"/>
        <rect x="4" y="8.5" width="16" height="14" rx="2"/>
        <rect x="11.5" y="13" width="1" height="5" rx="0.5" stroke="white"/>
    </svg>
</template>

<script>
export default {
    name: 'FlatBookedIcon',

    props: ['className']
}
</script>
