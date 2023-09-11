<template>
    <svg :class="className" width="108" height="55" viewBox="0 0 108 55" fill="none">
        <path d="M107.724 0.841797L54.2509 54.7596L0.777344 0.841797L107.724 0.841797Z"
              fill="url(#rotate-gradient)">
        </path>
        <defs>
            <linearGradient id="rotate-gradient" x1="15.3743" y1="0.841797" x2="15.3743" y2="40.0413"
                gradientUnits="userSpaceOnUse">
                <stop stop-opacity="0.01"></stop>
                <stop offset="1"></stop>
            </linearGradient>
        </defs>
    </svg>
</template>

<script>
export default {
    name: 'RotateGradientIcon',

    props: ['className']
};
</script>
