<template>
    <svg :class="className" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 6L5 2L9 6" stroke-width="2" stroke-linecap="round"/>
    </svg>
</template>

<script>
export default {
    name: 'ArrowIconUp',

    props: ['className']
}
</script>
