<template>
    <svg :class="className" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.64645 13.6464C8.84171 13.4512 9.15829 13.4512 9.35355 13.6464L12 16.2929L14.6464 13.6464C14.8417 13.4512 15.1583 13.4512 15.3536 13.6464C15.5488 13.8417 15.5488 14.1583 15.3536 14.3536L12 17.7071L8.64645 14.3536C8.45118 14.1583 8.45118 13.8417 8.64645 13.6464Z" fill="black"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3536 10.3536C15.1583 10.5488 14.8417 10.5488 14.6464 10.3536L12 7.70711L9.35355 10.3536C9.15829 10.5488 8.84171 10.5488 8.64645 10.3536C8.45118 10.1583 8.45118 9.84171 8.64645 9.64645L12 6.29289L15.3536 9.64645C15.5488 9.84171 15.5488 10.1583 15.3536 10.3536Z" fill="black"/>
    </svg>
</template>

<script>
export default {
    name: 'FloorChangeIcon',

    props: ['className']
};
</script>
