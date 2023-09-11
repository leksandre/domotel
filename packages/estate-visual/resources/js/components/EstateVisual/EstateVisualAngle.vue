<template>
    <div class="p-4 py-4 d-flex flex-column">
        <div class="form-group">
            <label :for="`field-${angle.id}-title`"
                   class="form-label">
                {{ translates.title }} <sup class="text-danger">*</sup>
            </label>
            <div>
                <input class="form-control"
                       required="required"
                       v-model="angle.title"
                       :name="`angles[${angle.id}][title]`"
                       :id="`field-${angle.id}-title`">
            </div>
        </div>
        <div class="form-group">
            <label :for="`field-${angle.id}-degree`"
                   class="form-label">
                {{ translates.degree }} <sup class="text-danger">*</sup>
            </label>
            <div>
                <input class="form-control"
                       required="required"
                       type="number"
                       v-model="angle.degree"
                       :name="`angles[${angle.id}][degree]`"
                       :id="`field-${angle.id}-degree`">
            </div>
        </div>
        <div class="form-group">
            <label :for="`field-${angle.id}-shift-horizontal`"
                   class="form-label">
                {{ translates.shiftHorizontal }}
            </label>
            <div>
                <input class="form-control"
                       type="number"
                       min="0"
                       max="100"
                       step="1"
                       v-model="angle.shift[0]"
                       :name="`angles[${angle.id}][shift][0]`"
                       :id="`field-${angle.id}-shift-horizontal`">
            </div>
        </div>
        <div class="form-group">
            <label :for="`field-${angle.id}-shift-vertical`"
                   class="form-label">
                {{ translates.shiftVertical }}
            </label>
            <div>
                <input class="form-control"
                       type="number"
                       min="0"
                       max="100"
                       step="1"
                       v-model="angle.shift[1]"
                       :name="`angles[${angle.id}][shift][1]`"
                       :id="`field-${angle.id}-shift-vertical`">
            </div>
        </div>
        <div class="form-group">
            <div class="btn-group">
                <label class="btn btn-default m-0 text-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2">
                        <path d="M23.845 8.124c-1.395-3.701-4.392-6.045-8.921-6.045-5.762 0-9.793 4.279-10.14 9.86-2.778 0.889-4.784 3.723-4.784 6.933 0 3.93 3.089 7.249 6.744 7.249h2.889c0.552 0 1-0.448 1-1s-0.448-1-1-1h-2.889c-2.572 0-4.776-2.404-4.776-5.249 0-2.514 1.763-4.783 3.974-5.163l0.907-0.156-0.080-0.916-0.008-0.011c0-4.871 3.205-8.545 8.161-8.545 3.972 0 6.204 1.957 7.236 5.295l0.214 0.688 0.721 0.015c3.715 0.078 6.972 3.092 6.972 6.837 0 3.408-2.259 7.206-5.678 7.206h-2.285c-0.552 0-1 0.448-1 1s0.448 1 1 1l2.277-0.003c5-0.132 7.605-4.908 7.605-9.203 0-4.616-3.617-8.305-8.14-8.791zM16.75 16.092c-0.006-0.006-0.008-0.011-0.011-0.016l-0.253-0.264c-0.139-0.146-0.323-0.219-0.508-0.218-0.184-0.002-0.368 0.072-0.509 0.218l-0.253 0.264c-0.005 0.005-0.006 0.011-0.011 0.016l-3.61 3.992c-0.28 0.292-0.28 0.764 0 1.058l0.252 0.171c0.28 0.292 0.732 0.197 1.011-0.095l2.128-2.373v10.076c0 0.552 0.448 1 1 1s1-0.448 1-1v-10.066l2.199 2.426c0.279 0.292 0.732 0.387 1.011 0.095l0.252-0.171c0.279-0.293 0.279-0.765 0-1.058z"></path>
                    </svg>
                    {{ translates.uploadImage }}
                    <input type="file" accept="image/*" class="d-none" @change="importRender($event)">
                    <input type="hidden" :name="`angles[${angle.id}][image_id]`" v-model="angle.render.id" />
                </label>
                <a class="btn btn-default m-0 text-wrap" role="button" data-bs-toggle="modal" :data-bs-target="`#maskImporter-${angle.id}`">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2">
                        <path d="M0.682 9.431l14.847 8.085c0.149 0.081 0.313 0.122 0.479 0.122 0.163 0 0.326-0.040 0.474-0.12l15.003-8.085c0.327-0.176 0.53-0.52 0.525-0.892s-0.216-0.711-0.547-0.88l-14.848-7.54c-0.283-0.143-0.617-0.144-0.902-0.002l-15.002 7.54c-0.332 0.167-0.545 0.505-0.551 0.877s0.196 0.717 0.521 0.895zM16.161 2.134l12.692 6.446-12.843 6.921-12.693-6.912zM31.292 15.010l-2.968-1.507-2.142 1.155 2.5 1.27-12.842 6.921-12.694-6.912 2.666-1.34-2.136-1.164-3.135 1.575c-0.332 0.167-0.545 0.505-0.551 0.877s0.196 0.717 0.521 0.895l14.847 8.085c0.149 0.081 0.313 0.122 0.479 0.122 0.163 0 0.326-0.040 0.474-0.12l15.003-8.085c0.327-0.176 0.53-0.52 0.525-0.892s-0.215-0.711-0.546-0.88zM31.292 22.010l-2.811-1.382-2.142 1.155 2.344 1.145-12.843 6.921-12.694-6.912 2.478-1.121-2.136-1.164-2.947 1.357c-0.332 0.167-0.545 0.505-0.551 0.877s0.196 0.717 0.521 0.895l14.847 8.085c0.149 0.081 0.313 0.122 0.479 0.122 0.163 0 0.326-0.040 0.475-0.12l15.003-8.085c0.327-0.176 0.53-0.52 0.525-0.892-0.005-0.373-0.215-0.712-0.546-0.88z"></path>
                    </svg>
                    {{ translates.uploadMasks }}
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <EstateVisualAngleRender
                    :angle-id="angle.id"
                    :render="angle.render"
                    :pointers="angle.pointers"
                    :masks="angle.masks"
                    @remove-render="removeRender" />
                <EstateVisualAnglePointers
                    :angle-id="angle.id"
                    :pointers="angle.pointers"
                    :pointer-types="pointerTypes"
                    @pointer-remove="removePointer"
                    @pointer-add="addPointer" />
            </div>
            <div class="col-md-5">
                <EstateVisualAngleMasks
                    :angle-id="angle.id"
                    :masks="angle.masks"
                    :elements="angle.elements"
                    @mask-remove="removeMask"
                    @mask-remove-link="removeMaskLink"
                    @mask-set-link="setMaskLink"
                    @mask-updated="maskUpdated" />
            </div>
        </div>
        <EstateModal :id="`maskImporter-${angle.id}`">
            <template v-slot:title>{{ translates.addingMasks }}</template>
            <template v-slot:body>
                <div class="nav-tabs-alt">
                    <ul class="nav nav-tabs nav-tabs-scroll-bar" role="tablist">
                        <li class="nav-item position-relative">
                            <a class="nav-link active"
                               :data-bs-target="`#import-tab-svg-${angle.id}`"
                               role="presentation"
                               data-bs-toggle="tab">SVG</a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="nav-link"
                               :data-bs-target="`#import-tab-inline-${angle.id}`"
                               role="presentation"
                               data-bs-toggle="tab">{{ translates.coords }}</a>
                        </li>
                    </ul>
                </div>
                <section class="mb-3">
                    <div class="no-border-xs">
                        <div class="tab-content">
                            <div role="tabpanel"
                                 class="tab-pane active"
                                 :id="`import-tab-svg-${angle.id}`">
                                <div class="py-4 d-flex flex-column">
                                    <div :class="{'d-none': !hasMaskFiles}">
                                        <div class="mask-file-name">
                                            <svg width="1em" height="1em" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                                <path d="M11.25 0H0.75C0.337875 0 0 0.337875 0 0.75V9.37725C0 9.78938 0.3375 10.1273 0.75 10.1273H11.25C11.6621 10.1273 12 9.78938 12 9.37725V0.75C12 0.337875 11.6621 0 11.25 0ZM11.25 9.37725H0.75V7.16775L3.76013 4.25063L6.86325 7.35C6.99113 7.50525 7.21875 7.48538 7.38037 7.36313L8.74988 6.12338L11.22 8.65913C11.2294 8.6685 11.2399 8.67563 11.25 8.6835V9.37725ZM11.25 7.61588L9.0465 5.361C8.91263 5.229 8.70337 5.21588 8.55487 5.32875L7.14188 6.5595L4.03688 3.48825C3.9705 3.408 3.87487 3.35925 3.771 3.35213C3.66787 3.34838 3.56588 3.38213 3.49013 3.45338L0.74925 6.12038V0.749625H11.2492V7.6155L11.25 7.61588ZM8.625 3.75263C9.03825 3.75263 9.37313 3.41738 9.37313 3.0045C9.37313 2.59163 9.03862 2.25638 8.625 2.25638C8.21138 2.25638 7.87687 2.59163 7.87687 3.0045C7.87687 3.41738 8.21138 3.75263 8.625 3.75263Z" fill="#222529"/>
                                            </svg>
                                            {{ getFirstMaskFilename }}
                                        </div>
                                        <button type="button" class="btn btn-secondary mt-4" @click.stop.prevent="resetMasksFileList()">{{ translates.delete }}</button>
                                    </div>
                                    <label class="btn btn-default m-0 w-fit" :class="{'d-none': hasMaskFiles}">
                                        <input name="svg" type="file" accept="image/svg+xml" class="d-none" @change.stop="setMasksFileList($event)">
                                        {{ translates.uploadSvg }}
                                    </label>
                                    <div class="form-check mt-3">
                                        <label class="m-0 w-fit" :class="{'d-none': !hasMaskFiles || !elementHasNumberAttribute}">
                                            <input type="checkbox" class="form-check-input" v-model="autolink" />
                                            {{ translates.autoLink }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel"
                                 class="tab-pane"
                                 :id="`import-tab-inline-${angle.id}`">
                                <div class="py-4 d-flex flex-column">
                                    <textarea name="coords" class="form-control" rows="5" v-model="maskCoords"></textarea>
                                    <small class="form-text text-muted">
                                        {{ translates.format }}<br>
                                        M446,183V363H326V183Z<br>
                                        M717,183V440H591V379H542V363H452V183Z<br>
                                        M1393,322V434h-43v6H1173V183h177V322Z
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
            <template v-slot:footer>
                <button class="btn btn-default" data-bs-dismiss="modal" @click.stop.prevent="importMasksByForm($event)">{{ translates.apply }}</button>
            </template>
        </EstateModal>
    </div>
</template>

<script>
import {
    getImageSizes,
    getMasksFromString,
    getMasksFromSvg,
    getUniqueKey,
    readFile,
    uploadFile
} from "./utils";
import EstateVisualAngleRender from "./EstateVisualAngleRender.vue";
import EstateVisualAngleMasks from "./EstateVisualAngleMasks.vue";
import EstateVisualAnglePointers from "./EstateVisualAnglePointers.vue";
import EstateModal from "./EstateModal.vue";

const FILE_FIRST_ELEMENT = 0;
const POINTER_TYPE_TEXT = 'text';

export default {
    name: "EstateVisualAngle",
    components: {
        EstateVisualAngleMasks,
        EstateModal,
        EstateVisualAngleRender,
        EstateVisualAnglePointers
    },
    data() {
        return {
            autolink: false,
            maskFiles: [],
            maskCoords: ''
        }
    },
    props: {
        angle: {
            type: Object,
            required: true
        },
        pointerTypes: {
            type: Array,
            required: true
        }
    },
    inject: ['translates', 'storageUrl', 'storageDisk', 'groups'],
    computed: {
        hasMaskFiles() {
            return this.maskFiles.length > 0;
        },
        getFirstMaskFilename() {
            return this.maskFiles[FILE_FIRST_ELEMENT]?.name ?? '-';
        },
        elementHasNumberAttribute() {
            return this.angle.elements.length && this.angle.elements[0]?.hasOwnProperty('serialNumber');
        },
        linkMaskToElement() {
            return this.elementHasNumberAttribute && this.autolink;
        }
    },
    methods: {
        async importRender(event) {
            await this.importRenderFile(event.target.files);
            event.target.value = null;
        },
        async importRenderFile(files) {
            if (!files.length) {
                this.$emit('alert', this.translates.error, this.translates.fileNotLoaded, 'error');

                return;
            }

            const render = files.item(FILE_FIRST_ELEMENT);
            if (!/^image\//.test(render.type)) {
                this.$emit('alert', this.translates.error, this.translates.invalidFileFormat, 'error');

                return;
            }

            let sizes = null;

            try {
                sizes = await getImageSizes(render);
            } catch (e) {
                this.$emit('alert', this.translates.error, `${this.translates.getImageSizesError}: ${e.message}`, 'error');

                return;
            }

            let image = null;

            try {
                image = await uploadFile(render, this.storageUrl, this.storageDisk, this.groups);
            } catch (e) {
                this.$emit('alert', this.translates.error, `${this.translates.uploadError}: ${e.message}`, 'error');

                return;
            }

            this.angle.render.id = image.id;
            this.angle.render.path = image.url;
            this.angle.render.width = sizes.width;
            this.angle.render.height = sizes.height;

            this.$emit('alert', this.translates.modified, this.translates.imageUploaded, 'info');
        },
        removeRender() {
            this.angle.render.id = null;
            this.angle.render.path = null;
            this.angle.render.width = this.angle.render.height = 0;
        },
        setMasksFileList(event) {
            this.maskFiles = [...event.target.files];
            event.target.value = null;
        },
        resetMasksFileList() {
            this.maskFiles = [];
        },
        async importMasksByForm(event)
        {
            let cnt = 0;

            if (this.maskFiles.length) {
                cnt += await this.importMasksFromSvg(this.maskFiles.shift());
                this.maskFiles = [];
                this.autolink = false;
            }

            if (this.maskCoords) {
                getMasksFromString(this.maskCoords)
                    .forEach(coords => {
                        if (this._addMask(coords)) {
                            cnt++;
                        }
                    });

                this.maskCoords = null;
            }

            if (cnt) {
                this.$emit('alert', this.translates.modified, `${this.translates.maskAdded}: ${cnt}`, 'info');
            } else {
                this.$emit('alert', this.translates.error, this.translates.maskNotAdded, 'warning');
            }

            window.Bootstrap.Modal.getOrCreateInstance(event.target.closest('.modal'))?.hide();
        },
        async importMasksFromSvg(svg) {
            let cnt = 0;

            if (svg.type !== 'image/svg+xml') {
                this.$emit('alert', this.translates.error, this.translates.invalidFileFormat, 'error');

                return cnt;
            }

            let svgData = null;

            try {
                svgData = await readFile(svg);
            } catch (e) {
                this.$emit('alert', this.translates.error, e.message, 'error');

                return cnt;
            }

            const masks = getMasksFromSvg(svgData);

            if (!masks.length) {
                this.$emit('alert', this.translates.error, this.translates.maskNotFound);

                return cnt;
            }

            for (const mask of masks) {
                if (this._addMask(mask.coords)) {
                    if (this.linkMaskToElement) {
                        const targetMask = this.angle.masks.find(cMask => cMask.coords === mask.coords);
                        if (targetMask) {
                            this.$eventBus.$emit('link-mask-to-element', this.angle.id, targetMask.id, mask.serialNumber);
                        }
                    }
                    cnt++;
                }
            }

            return cnt;
        },
        _addMask(coords) {
            if (!this._isUniqueMask(coords)) {
                return false;
            }

            this.angle.masks.push({
                id: getUniqueKey(),
                type: null,
                element_id: 0,
                pointer: [0, 0],
                coords
            });

            return true;
        },
        _isUniqueMask(coords) {
            return !this.angle.masks.length
                || !this.angle.masks.find(mask => mask.coords.toLowerCase() === coords.toLowerCase());
        },
        removeMask(id) {
            if (!this.angle.masks.length) {
                return;
            }

            this.angle.masks = this.angle.masks.filter((el) => el.id !== id);
            this.$emit('alert', this.translates.modified, this.translates.maskDeleted, 'info');
        },
        removeMaskLink(id) {
            if (!this.angle.masks.length) {
                return;
            }

            let mask = this.angle.masks.find((mask) => mask.id === id);

            if (!mask) {
                return;
            }

            mask = Object.assign(
                mask,
                {
                    type: null,
                    element_id: 0,
                    value: null,
                    elementTitle: null,
                }
            );

            this.$emit('alert', this.translates.modified, this.translates.maskLinkRemoved, 'info');
        },
        setMaskLink(newVal) {
            let mask = this.angle.masks.find((mask) => mask.id === newVal.id);

            if (!mask) {
                return;
            }

            mask = Object.assign(mask, newVal);
            this.$emit('alert', this.translates.modified, this.translates.maskLinked, 'info');
        },
        maskUpdated(id) {
            this.$emit('alert', this.translates.modified, this.translates.maskUpdated, 'info');
        },

        //Pointers
        removePointer(id) {
            if (!this.angle.pointers.length) {
                return;
            }

            this.angle.pointers = this.angle.pointers.filter((el) => el.id !== id);
            // this.$emit('alert', this.translates.modified, this.translates.pointerDeleted, 'info');
        },
        updatePointer() {

        },
        addPointer() {
            this.angle.pointers.push({
                id: getUniqueKey(),
                type: POINTER_TYPE_TEXT,
                data: {
                    panorama: null,
                    text: this.translates.pointerTitle
                },
                position: [0, 0]
            });

            return true;
        },
    }
}
</script>
