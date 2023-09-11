<template>
    <div class="kelnik-estate-visual form-group">
        <div class="nav-tabs-alt">
            <ul class="nav nav-tabs nav-tabs-scroll-bar" role="tablist">
                <li class="nav-item position-relative" v-for="(el, index) of angles">
                    <a class="nav-link"
                       :class="{'active': index === currentTab}"
                       :data-bs-target="`#tab-${el.id}`"
                       @click="setCurrentTab(index)"
                       role="tablist"
                       data-bs-toggle="tab">{{ el.title }}</a>
                    <a @click="removeAngle(el.id)" v-if="canDeleteAngle" class="position-absolute top-0 end-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 32 32">
                            <path d="M21.66,10.34a1,1,0,0,0-1.42,0L16,14.59l-4.24-4.25a1,1,0,0,0-1.42,1.42L14.59,16l-4.25,4.24a1,1,0,0,0,1.42,1.42L16,17.41l4.24,4.25a1,1,0,0,0,1.42-1.42L17.41,16l4.25-4.24A1,1,0,0,0,21.66,10.34Z"/>
                        </svg>
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a @click="addAngle()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2">
                            <path d="M16 0c-8.836 0-16 7.163-16 16s7.163 16 16 16c8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 30.032c-7.72 0-14-6.312-14-14.032s6.28-14 14-14 14 6.28 14 14-6.28 14.032-14 14.032zM23 15h-6v-6c0-0.552-0.448-1-1-1s-1 0.448-1 1v6h-6c-0.552 0-1 0.448-1 1s0.448 1 1 1h6v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6h6c0.552 0 1-0.448 1-1s-0.448-1-1-1z"></path>
                        </svg>
                        {{ translates.addAngle }}
                    </a>
                </li>
            </ul>
        </div>
        <section class="mb-3">
            <div class="no-border-xs">
                <div class="tab-content">
                    <div v-for="(el, index) of angles" role="tabpanel"
                         class="tab-pane"
                         :class="{'active': index === currentTab}"
                         :id="`tab-${el.id}`">
                        <EstateVisualAngle :angle="el" :pointer-types="pointerTypes" @alert="alert" />
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import EstateVisualAngle from "./EstateVisualAngle.vue";
import {getUniqueKey} from "./utils";
import Vue from "vue";

Vue.prototype.$eventBus = new Vue();

const DEFAULT_TAB_INDEX = 0;
const DEFAULT_DEGREE = 0;
const MIN_NUMBER = 0;

export default {
    name: 'EstateVisual',
    components: {
        EstateVisualAngle
    },
    data() {
        return {
            translates: {
                title: 'Title',
                angle: 'Angle',
                addAngle: 'Add angle',
                uploadImage: 'Upload image',
                uploadMasks: 'Upload masks',
                confirmDeleteAngle: 'Delete angle?',
                masks: 'Masks',
                data: 'Data',
                emptyList: 'The list is empty',
                error: 'Error',
                modified: 'Modified',
                fileNotLoaded: 'File not loaded',
                mask: 'Mask',
                maskNotFound: 'Mask not found',
                maskAdded: 'Mask added',
                maskNotAdded: 'Mask not added',
                maskDeleted: 'Mask deleted',
                maskLinkRemoved: 'Mask link removed',
                maskLinked: 'Mask linked',
                maskUpdated: 'Mask updated',
                confirmDeleteMask: 'Delete mask?',
                invalidFileFormat: 'Invalid file format',
                getImageSizesError: 'Failed to get the dimensions of the image',
                uploadError: 'Error on uploading file',
                imageUploaded: 'Image uploaded',
                errorLoadingData: 'Error loading data',
                setLink: 'Set link',
                nextStepHeader: 'Next step',
                nextStep: 'Next step',
                coords: 'Coords',
                binding: 'Binding',
                noBinding: 'No binding',
                close: 'Close',
                link: 'Link',
                apply: 'Apply',
                format: 'Format',
                uploadSvg: 'Upload SVG',
                addingMasks: 'Adding masks',
                degree: 'Degree',
                pointer: 'Pointer',
                mobileStartPercentage: 'Start Percentage',
                pointers: 'Pointers',
                addPointer: 'Add pointer',
                pointerTitle: 'Pointer title',
                pointerPopWidgetCode: 'Planoplan widget code',
                pointerCoords: 'Coords (x, y)',
                autoLink: 'Link mask to element automatically'
            },
            angles: [],
            data: {
                nextStep: null,
                steps: [],
                elements: [],
                titles: {}
            },
            currentTab: DEFAULT_TAB_INDEX,
            lastAngleNumber: MIN_NUMBER,
            pointerTypes: null
        };
    },
    provide() {
        return {
            translates: this.translates,
            storageUrl: this.$attrs['storage-url'],
            storageDisk: this.$attrs['storage-disk'],
            groups: this.$attrs.groups,
            data: this.data
        }
    },
    created() {
        if (this.$attrs.translates) {
            try {
                this.translates = Object.assign(
                    this.translates,
                    JSON.parse(
                        Buffer.from(this.$attrs.translates, 'base64').toString()
                    )
                );
            } catch (error) {
            }
        }

        this.defaultAxiosConfig = {
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf_token"]')?.content || ''
            }
        };

        String.prototype.hashCode = function () {
            return this.split('').reduce(function (a, b) {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, '0');
        };

        this.loadData();
    },
    computed: {
        canDeleteAngle() {
            return this.angles.length > 1;
        }
    },
    methods: {
        loadData() {
            axios.get(this.$attrs.url)
                .then((resp) => {
                    this.angles = resp.data.data.angles;
                    this.data.nextStep = resp.data.data.nextStep;
                    this.data.steps = resp.data.data.steps;
                    this.data.elements = resp.data.data.elements;
                    this.data.titles = resp.data.data.titles;

                    this.angles.forEach(angle => angle.elements = this.data.elements);
                    this.pointerTypes = resp.data.data.pointerTypes;
                })
                .then(() => this.lastAngleNumber = this.angles.length)
                .catch((err) => {
                    this.addAngle();
                    this.alert(this.translates.error, this.translates.errorLoadingData, 'error');
                    console.error(err.message);
                });
        },
        alert(title, message, type = 'warning') {
            const toastWrapper = document.querySelector('[data-controller="toast"]');
            const toastController = this.$attrs.stimulus.application.getControllerForElementAndIdentifier(toastWrapper, 'toast');

            if (toastController) {
                toastController.alert(title, message, type);

                return;
            }

            alert(title + '\n' + message);
        },
        setCurrentTab(index) {
            this.currentTab = index;
        },
        addAngle() {
            this.angles.push(this.makeAngle());
            this.lastAngleNumber++;
        },
        removeAngle(elId) {
            if (!confirm(this.translates.confirmDeleteAngle) || !this.canDeleteAngle) {
                return;
            }

            this.angles = this.angles.filter((el) => el.id !== elId);
            this.currentTab = DEFAULT_TAB_INDEX;
        },
        makeAngle() {
            let angleCount = this.lastAngleNumber;

            return {
                id: getUniqueKey(),
                title: `${this.translates.angle} ${++angleCount}`,
                degree: DEFAULT_DEGREE,
                shift: [0, 0],
                render: {id: null, path: null, width: 0, height: 0},
                masks: [],
                pointers: [],
                elements: this.data.elements
            };
        }
    }
}
</script>

<style lang="scss">
@import "EstateVisual";
</style>
