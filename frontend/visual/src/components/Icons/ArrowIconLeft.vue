<template>
    <svg :class="className" width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M6 9L2 5L6 1" stroke-width="2" stroke-linecap="round"/>
    </svg>
</template>

<script>
export default {
    name: 'ArrowIconLeft',

    props: ['className']
}
</script>
