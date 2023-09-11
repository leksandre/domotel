<template>
    <section class="mt-3">
        <legend class="text-black">{{ translates.pointers }}</legend>
        <fieldset class="md-2 border rounded">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <tbody>
                    <tr v-for="(pointer, index) in pointers">
                        <td class="pointer">
                            <div class="body">
                                <div class="type">
                                    <span>Тип: </span>
                                    <span>
                                        <select class="form-control"
                                           :name="`angles[${angleId}][pointers][${pointer.id}][type]`"
                                           v-model="pointer.type">
                                            <option v-for="(type) in pointerTypes"
                                                    :value="`${type.name}`"
                                                    :selected="type.name === pointer.type">{{ type.title }}</option>
                                        </select>
                                    </span>
                                </div>
                                <div class="coords">
                                    <span>{{ translates.pointerCoords }}</span>
                                    <span>
                                        <input type="number"
                                                   min="0"
                                                   step="1"
                                                   class="form-control"
                                                   style="width: 90px"
                                                   :name="`angles[${angleId}][pointers][${pointer.id}][position][0]`"
                                                   v-model="pointer.position[0]" />&nbsp;
                                        <input type="number"
                                                   min="0"
                                                   step="1"
                                                   class="form-control"
                                                   style="width: 90px"
                                                   :name="`angles[${angleId}][pointers][${pointer.id}][position][1]`"
                                                   v-model="pointer.position[1]" />
                                        </span>
                                </div>
                                <div class="data" v-if="pointerIsPanorama(pointer)">
                                    <input type="text"
                                           class="form-control"
                                           :placeholder="`${translates.pointerPopWidgetCode}`"
                                           :name="`angles[${angleId}][pointers][${pointer.id}][data][uid]`"
                                           v-model="pointer.data.uid" />
                                </div>
                                <div class="data" v-else>
                                    <input type="text"
                                           class="form-control"
                                           :placeholder="`${translates.pointerTitle}`"
                                           :name="`angles[${angleId}][pointers][${pointer.id}][data][text]`"
                                           v-model="pointer.data.text" />
                                </div>
                            </div>
                            <div class="menu">
                                <a @click="removePointer(pointer.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 32 32">
                                        <path d="M21.66,10.34a1,1,0,0,0-1.42,0L16,14.59l-4.24-4.25a1,1,0,0,0-1.42,1.42L14.59,16l-4.25,4.24a1,1,0,0,0,1.42,1.42L16,17.41l4.24,4.25a1,1,0,0,0,1.42-1.42L17.41,16l4.25-4.24A1,1,0,0,0,21.66,10.34Z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="hasNoPointers">
                        <td class="text-center" colspan="3">{{ translates.emptyList }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
        <div class="form-group mt-3">
            <button class="btn btn-default" @click.stop.prevent="addPointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" class="me-2" viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 0c-8.836 0-16 7.163-16 16s7.163 16 16 16c8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 30.032c-7.72 0-14-6.312-14-14.032s6.28-14 14-14 14 6.28 14 14-6.28 14.032-14 14.032zM23 15h-6v-6c0-0.552-0.448-1-1-1s-1 0.448-1 1v6h-6c-0.552 0-1 0.448-1 1s0.448 1 1 1h6v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6h6c0.552 0 1-0.448 1-1s-0.448-1-1-1z"></path>
                </svg>
                {{ translates.addPointer }}
            </button>
        </div>
    </section>
</template>

<script>
export default {
    name: "EstateVisualAnglePointers",
    props: {
        angleId: {
            type: Number | String,
            required: true
        },
        pointers: {
            type: Array,
            required: true
        },
        pointerTypes: {
            type: Array,
            required: true
        }
    },
    inject: ['translates'],
    computed: {
        hasNoPointers() {
            return !this.pointers.length;
        },
        pointerIsPanorama() {
            return (pointer) => pointer.type === 'panorama';
        }
    },
    methods: {
        addPointer() {
           this.$emit('pointer-add');
        },
        removePointer(id) {
            this.$emit('pointer-remove', id);
        }
    }
}
</script>
