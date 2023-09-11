<template>
    <fieldset class="md-2">
        <div class="form-group">
            <svg class="visual-canvas rounded"
                 :class="{'no-bg': render.path}"
                 :viewBox="[`0 0 ${render.width} ${render.height}`]"
                 ref="svg"
                 preserveAspectRatio="xMidYMid slice"
            >
                <image v-if="hasRender" x="0" y="0" width="100%" height="100%" :xlink:href="render.path" />
                <path v-for="mask of masks"
                      :id="mask.id"
                      :d="mask.coords"
                      :class="{'has-link': mask.type}"
                      @click.stop="$eventBus.$emit('click-on-mask-path', angleId, mask.id)"
                      ref="masks"
                />
                <EstateVisualAngleRenderPointer v-for="mask of masks"
                                                :key="`m-${mask.id}`"
                                                :x="getPosition(mask.pointer, 0)"
                                                :y="getPosition(mask.pointer, 1)"
                                                :title="mask.elementTitle" />
                <EstateVisualAngleRenderPointer v-for="pointer of pointers"
                                                :key="`p-${pointer.id}`"
                                                :x="getPosition(pointer.position, 0)"
                                                :y="getPosition(pointer.position, 1)"
                                                :title="getTitle(pointer)" />
            </svg>
        </div>
        <div class="form-group mb-3" v-if="render.path">
            <span class="badge bg-secondary me-2">{{ renderFileExtension }}</span> {{ renderFileName }}
            <a @click="removeRender()" class="ms-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.4em" height="1.4em" viewBox="0 0 32 32">
                    <path d="M21.66,10.34a1,1,0,0,0-1.42,0L16,14.59l-4.24-4.25a1,1,0,0,0-1.42,1.42L14.59,16l-4.25,4.24a1,1,0,0,0,1.42,1.42L16,17.41l4.24,4.25a1,1,0,0,0,1.42-1.42L17.41,16l4.25-4.24A1,1,0,0,0,21.66,10.34Z"/>
                </svg>
            </a>
        </div>
    </fieldset>
</template>

<script>
import {fileExtension, fileName} from "./utils";
import EstateVisualAngleRenderPointer from "./EstateVisualAngleRenderPointer.vue";

const POINTER_TYPE_PANORAMA = 'panorama';

export default {
    name: "EstateVisualAngleRender",
    components: {
        EstateVisualAngleRenderPointer
    },
    props: {
        angleId: {
            type: Number | String,
            required: true
        },
        render: {
            type: Object,
            required: true
        },
        pointers: {
            type: Array
        },
        masks: {
            type: Array,
            required: true
        }
    },
    inject: ['translates'],
    created() {
        this.$eventBus.$on('sync-masks-list', this.handleSyncMasks);
    },
    computed: {
        hasRender() {
            return !!(this.render.id || null);
        },
        renderFileName() {
            return fileName(this.render.path);
        },
        renderFileExtension() {
            return fileExtension(fileName(this.render.path));
        }
    },
    methods: {
        removeRender() {
            this.$emit('remove-render');
        },
        getPosition(position, num) {
            const isString = typeof position === 'string';

            if (!Array.isArray(position) && !isString) {
                return 0;
            }

            if (isString) {
                position = position.trim().split(',').slice(0, 2).map((el) => Number(el));
            }

            return position[num] || 0;
        },
        getTitle(pointer) {
            if (Object.hasOwn(pointer, 'type') && pointer.type === POINTER_TYPE_PANORAMA) {
                return '360';
            }

            return pointer.data.text || '';
        },
        handleSyncMasks(angleId) {
            if (this.render.path && angleId === this.angleId) {
                this.$forceUpdate();
            }
        }
    }
}
</script>
