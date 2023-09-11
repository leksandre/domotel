<template>
    <g class="visual-canvas__render-pointers">
        <template v-for="(pointer, index) in renderPointers">
            <foreignObject
                :key="index"
                :x="pointer.left"
                :y="pointer.top"
                :id="pointer.id"
                width="1"
                height="1"
                requiredExtensions="http://www.w3.org/1999/xhtml"
                class="visual-render-pointer">

                <template v-if="pointer.data.text">
                    <div xmlns="http://www.w3.org/1999/xhtml"
                         class="visual-render-pointer__item">
                        <div class="visual-render-pointer__info">
                            {{ pointer.data.text }}
                        </div>
                    </div>
                </template>

                <template v-if="pointer.data.icon">
                    <div xmlns="http://www.w3.org/1999/xhtml"
                         class="visual-render-pointer__icon-container"
                         :style="{width: `${pointer.data.icon.width}px`, height: `${pointer.data.icon.height}px`}">
                        <img class="visual-render-pointer__icon"
                             :src="pointer.data.icon.src"/>
                    </div>
                </template>
            </foreignObject>
        </template>
    </g>
</template>

<script>
export default {
    name: 'RenderPointerComponent',

    props: {
        renderPointers: Array
    }
};
</script>

<style lang="scss">
    @import "RenderPointerComponent";
</style>
