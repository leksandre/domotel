<template>
    <foreignObject v-if="Number(x) > 0 && title"
                   :x="x"
                   :y="y"
                   width="1"
                   height="1"
                   requiredExtensions="http://www.w3.org/1999/xhtml"
                   class="visual-canvas__pointer">
        <div xmlns="http://www.w3.org/1999/xhtml" class="visual-canvas__pointer-item visual-canvas__pointer-item_theme_render">
            <div class="visual-canvas__pointer-text">{{ title }}</div>
        </div>
    </foreignObject>
</template>

<script>
export default {
    name: "EstateVisualAngleRenderPointer",
    props: {
        x: {
            type: Number | String,
            required: true
        },
        y: {
            type: Number | String,
            required: true
        },
        title: {
            type: String
        }
    }
}
</script>
