<template>
    <g class="visual-canvas__panoramas">
        <template v-for="icon in panorama">
            <foreignObject
                :key="icon.id"
                :x="icon.left"
                :y="icon.top"
                :id="icon.id"
                width="1"
                height="1"
                requiredExtensions="http://www.w3.org/1999/xhtml"
                class="visual-panorama"
                @click="openPopup(icon.data.uid)">

                <div xmlns="http://www.w3.org/1999/xhtml"
                     class="visual-panorama__item">
                    <button type="button"
                            class="visual-panorama__info">
                        360&#176;
                    </button>
                </div>
            </foreignObject>
        </template>
    </g>
</template>

<script>
export default {
    props: {
        panorama: Array
    },

    mounted() {
        const planoplanScript = document.createElement('script');

        planoplanScript.setAttribute('src', 'https://widget.planoplan.com/etc/multiwidget/release/v4/static/js/main.js?v=2');

        document.body.appendChild(planoplanScript);
    },

    methods: {
        openPopup(uid) {
            this.$emit('open-planoplan', uid);
        }
    }
};
</script>

<style lang="scss">
    @import "./PanoramaComponent.scss";
</style>
