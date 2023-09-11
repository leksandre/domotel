<template>
    <fieldset class="md-2 border rounded">
        <div class="nav-tabs-alt">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active"
                       :data-bs-target="`#tab-${angleId}-data-to-masks`"
                       role="presentation"
                       data-bs-toggle="tab">{{ translates.data }}</a>
                </li>
                <li class="nav-item position-relative">
                    <a class="nav-link"
                       :data-bs-target="`#tab-${angleId}-masks-to-data`"
                       role="presentation"
                       data-bs-toggle="tab">{{ translates.masks }}</a>
                </li>
            </ul>
        </div>
        <section>
            <div class="no-border-xs">
                <div class="tab-content">
                    <div role="tabpanel"
                         class="tab-pane active"
                         :id="`tab-${angleId}-data-to-masks`">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <tr v-for="(el, index) of elements">
                                        <td class="text-start">
                                            {{ el.title }}
                                            <span v-if="el.maskId"
                                                  class="ms-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 32 32">
                                                    <path d="M1.060 29.448c0.010 0 0.022 0 0.034-0.001 0.506-0.017 0.825-0.409 0.868-0.913 0.034-0.371 1.030-9.347 15.039-9.337l0.032 5.739c0 0.387 0.223 0.739 0.573 0.904 0.346 0.166 0.764 0.115 1.061-0.132l12.968-10.743c0.233-0.191 0.366-0.475 0.365-0.774s-0.136-0.584-0.368-0.774l-12.967-10.643c-0.299-0.244-0.712-0.291-1.061-0.128-0.349 0.166-0.572 0.518-0.572 0.903l-0.032 5.614c-5.811 0.184-10.312 2.053-13.229 5.467-4.748 5.556-3.688 13.63-3.639 13.966 0.074 0.49 0.433 0.85 0.926 0.85zM18.033 17.182h-0.002c-10.007 0.006-13.831 3.385-16.015 6.37 0.32-2.39 1.252-5.273 3.281-7.626 2.698-3.128 7.045-4.777 12.736-4.777 0.552 0 1-0.447 1-1v-4.493l10.389 8.542-10.389 8.622v-4.637c0-0.265-0.105-0.52-0.294-0.708-0.187-0.187-0.441-0.293-0.706-0.293z"></path>
                                                </svg>
                                                <span class="badge bg-success">{{ translates.mask.toUpperCase() }}</span> {{ el.maskTitle }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a :class="{'invisible': !elementCanLink(el)}"
                                               class="dropdown-toggle mask-link mx-2"
                                               @click="toggleDropdown"
                                               aria-expanded="false">
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="link" width="1.2em" height="1.2em" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor" d="M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z"></path>
                                                </svg>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li v-for="(mask, index) in masks">
                                                    <a v-if="!mask.type"
                                                       class="dropdown-item"
                                                       @click="linkElementToMask($event.target, el, mask, index + 1)">
                                                        <span class="badge bg-secondary">{{ translates.mask.toUpperCase() }}</span> {{ index + 1 }}
                                                    </a>
                                                </li>
                                            </ul>
                                            <a class="mx-2"
                                               v-if="elementHasLink(el)"
                                               @click="removeMaskLinkByElement(el)">
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="unlink" width="1.2em" height="1.2em" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor" d="M304.083 405.907c4.686 4.686 4.686 12.284 0 16.971l-44.674 44.674c-59.263 59.262-155.693 59.266-214.961 0-59.264-59.265-59.264-155.696 0-214.96l44.675-44.675c4.686-4.686 12.284-4.686 16.971 0l39.598 39.598c4.686 4.686 4.686 12.284 0 16.971l-44.675 44.674c-28.072 28.073-28.072 73.75 0 101.823 28.072 28.072 73.75 28.073 101.824 0l44.674-44.674c4.686-4.686 12.284-4.686 16.971 0l39.597 39.598zm-56.568-260.216c4.686 4.686 12.284 4.686 16.971 0l44.674-44.674c28.072-28.075 73.75-28.073 101.824 0 28.072 28.073 28.072 73.75 0 101.823l-44.675 44.674c-4.686 4.686-4.686 12.284 0 16.971l39.598 39.598c4.686 4.686 12.284 4.686 16.971 0l44.675-44.675c59.265-59.265 59.265-155.695 0-214.96-59.266-59.264-155.695-59.264-214.961 0l-44.674 44.674c-4.686 4.686-4.686 12.284 0 16.971l39.597 39.598zm234.828 359.28l22.627-22.627c9.373-9.373 9.373-24.569 0-33.941L63.598 7.029c-9.373-9.373-24.569-9.373-33.941 0L7.029 29.657c-9.373 9.373-9.373 24.569 0 33.941l441.373 441.373c9.373 9.372 24.569 9.372 33.941 0z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="hasNoData">
                                        <td class="text-center">{{ translates.emptyList }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel"
                         class="tab-pane"
                         :id="`tab-${angleId}-masks-to-data`">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <tr v-for="(mask, index) in masks" :class="{'highlight': mask.highlight}">
                                        <td class="text-start">
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][type]`" :value="mask.type" />
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][value]`" :value="mask.value" />
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][element_id]`" :value="mask.element_id" />
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][pointer][0]`" :value="getPointerPosition(mask, 0)" />
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][pointer][1]`" :value="getPointerPosition(mask, 1)" />
                                            <input type="hidden" :name="`angles[${angleId}][masks][${mask.id}][coords]`" :value="mask.coords" />
                                            <span class="badge bg-secondary" :class="{'bg-success': mask.type}">{{ translates.mask.toUpperCase() }}</span> {{ index + 1 }}
                                            <div v-if="mask.type"
                                                 class="d-inline ms-2">
                                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 32 32">
                                                    <path d="M1.060 29.448c0.010 0 0.022 0 0.034-0.001 0.506-0.017 0.825-0.409 0.868-0.913 0.034-0.371 1.030-9.347 15.039-9.337l0.032 5.739c0 0.387 0.223 0.739 0.573 0.904 0.346 0.166 0.764 0.115 1.061-0.132l12.968-10.743c0.233-0.191 0.366-0.475 0.365-0.774s-0.136-0.584-0.368-0.774l-12.967-10.643c-0.299-0.244-0.712-0.291-1.061-0.128-0.349 0.166-0.572 0.518-0.572 0.903l-0.032 5.614c-5.811 0.184-10.312 2.053-13.229 5.467-4.748 5.556-3.688 13.63-3.639 13.966 0.074 0.49 0.433 0.85 0.926 0.85zM18.033 17.182h-0.002c-10.007 0.006-13.831 3.385-16.015 6.37 0.32-2.39 1.252-5.273 3.281-7.626 2.698-3.128 7.045-4.777 12.736-4.777 0.552 0 1-0.447 1-1v-4.493l10.389 8.542-10.389 8.622v-4.637c0-0.265-0.105-0.52-0.294-0.708-0.187-0.187-0.441-0.293-0.706-0.293z"></path>
                                                </svg>
                                                <a v-if="typeIsUrl(mask.type)" :href="mask.value" target="_blank">{{ mask.value }}</a>
                                                <span v-if="typeIsStep(mask.type)">{{ mask.elementTitle }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a v-if="typeIsEmpty(mask.type)"
                                               class="dropdown-toggle mask-link ms-2"
                                               @click="toggleDropdown"
                                               aria-expanded="false">
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="link" width="1.2em" height="1.2em" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor" d="M326.612 185.391c59.747 59.809 58.927 155.698.36 214.59-.11.12-.24.25-.36.37l-67.2 67.2c-59.27 59.27-155.699 59.262-214.96 0-59.27-59.26-59.27-155.7 0-214.96l37.106-37.106c9.84-9.84 26.786-3.3 27.294 10.606.648 17.722 3.826 35.527 9.69 52.721 1.986 5.822.567 12.262-3.783 16.612l-13.087 13.087c-28.026 28.026-28.905 73.66-1.155 101.96 28.024 28.579 74.086 28.749 102.325.51l67.2-67.19c28.191-28.191 28.073-73.757 0-101.83-3.701-3.694-7.429-6.564-10.341-8.569a16.037 16.037 0 0 1-6.947-12.606c-.396-10.567 3.348-21.456 11.698-29.806l21.054-21.055c5.521-5.521 14.182-6.199 20.584-1.731a152.482 152.482 0 0 1 20.522 17.197zM467.547 44.449c-59.261-59.262-155.69-59.27-214.96 0l-67.2 67.2c-.12.12-.25.25-.36.37-58.566 58.892-59.387 154.781.36 214.59a152.454 152.454 0 0 0 20.521 17.196c6.402 4.468 15.064 3.789 20.584-1.731l21.054-21.055c8.35-8.35 12.094-19.239 11.698-29.806a16.037 16.037 0 0 0-6.947-12.606c-2.912-2.005-6.64-4.875-10.341-8.569-28.073-28.073-28.191-73.639 0-101.83l67.2-67.19c28.239-28.239 74.3-28.069 102.325.51 27.75 28.3 26.872 73.934-1.155 101.96l-13.087 13.087c-4.35 4.35-5.769 10.79-3.783 16.612 5.864 17.194 9.042 34.999 9.69 52.721.509 13.906 17.454 20.446 27.294 10.606l37.106-37.106c59.271-59.259 59.271-155.699.001-214.959z"></path>
                                                </svg>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><span class="dropdown-header">{{ translates.nextStepHeader }}</span></li>
                                                <li v-for="el of elements"
                                                    v-if="!el.maskId">
                                                    <a class="dropdown-item"
                                                       @click="linkElementToMask($event.target, el, mask, index + 1)">{{ el.title }}</a>
                                                </li>
                                            </ul>

                                            <a v-if="!typeIsEmpty(mask.type)" class="ms-2" @click="removeMaskLink(mask)">
                                                <svg focusable="false" data-prefix="fas" data-icon="unlink" width="1.2em" height="1.2em" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor" d="M304.083 405.907c4.686 4.686 4.686 12.284 0 16.971l-44.674 44.674c-59.263 59.262-155.693 59.266-214.961 0-59.264-59.265-59.264-155.696 0-214.96l44.675-44.675c4.686-4.686 12.284-4.686 16.971 0l39.598 39.598c4.686 4.686 4.686 12.284 0 16.971l-44.675 44.674c-28.072 28.073-28.072 73.75 0 101.823 28.072 28.072 73.75 28.073 101.824 0l44.674-44.674c4.686-4.686 12.284-4.686 16.971 0l39.597 39.598zm-56.568-260.216c4.686 4.686 12.284 4.686 16.971 0l44.674-44.674c28.072-28.075 73.75-28.073 101.824 0 28.072 28.073 28.072 73.75 0 101.823l-44.675 44.674c-4.686 4.686-4.686 12.284 0 16.971l39.598 39.598c4.686 4.686 12.284 4.686 16.971 0l44.675-44.675c59.265-59.265 59.265-155.695 0-214.96-59.266-59.264-155.695-59.264-214.961 0l-44.674 44.674c-4.686 4.686-4.686 12.284 0 16.971l39.597 39.598zm234.828 359.28l22.627-22.627c9.373-9.373 9.373-24.569 0-33.941L63.598 7.029c-9.373-9.373-24.569-9.373-33.941 0L7.029 29.657c-9.373 9.373-9.373 24.569 0 33.941l441.373 441.373c9.373 9.372 24.569 9.372 33.941 0z"></path>
                                                </svg>
                                            </a>
                                            <a class="ms-2" @click="openModal(mask, index)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 32 32">
                                                    <path d="M30.133 1.552c-1.090-1.044-2.291-1.573-3.574-1.573-2.006 0-3.47 1.296-3.87 1.693-0.564 0.558-19.786 19.788-19.786 19.788-0.126 0.126-0.217 0.284-0.264 0.456-0.433 1.602-2.605 8.71-2.627 8.782-0.112 0.364-0.012 0.761 0.256 1.029 0.193 0.192 0.45 0.295 0.713 0.295 0.104 0 0.208-0.016 0.31-0.049 0.073-0.024 7.41-2.395 8.618-2.756 0.159-0.048 0.305-0.134 0.423-0.251 0.763-0.754 18.691-18.483 19.881-19.712 1.231-1.268 1.843-2.59 1.819-3.925-0.025-1.319-0.664-2.589-1.901-3.776zM22.37 4.87c0.509 0.123 1.711 0.527 2.938 1.765 1.24 1.251 1.575 2.681 1.638 3.007-3.932 3.912-12.983 12.867-16.551 16.396-0.329-0.767-0.862-1.692-1.719-2.555-1.046-1.054-2.111-1.649-2.932-1.984 3.531-3.532 12.753-12.757 16.625-16.628zM4.387 23.186c0.55 0.146 1.691 0.57 2.854 1.742 0.896 0.904 1.319 1.9 1.509 2.508-1.39 0.447-4.434 1.497-6.367 2.121 0.573-1.886 1.541-4.822 2.004-6.371zM28.763 7.824c-0.041 0.042-0.109 0.11-0.19 0.192-0.316-0.814-0.87-1.86-1.831-2.828-0.981-0.989-1.976-1.572-2.773-1.917 0.068-0.067 0.12-0.12 0.141-0.14 0.114-0.113 1.153-1.106 2.447-1.106 0.745 0 1.477 0.34 2.175 1.010 0.828 0.795 1.256 1.579 1.27 2.331 0.014 0.768-0.404 1.595-1.24 2.458z"></path>
                                                </svg>
                                            </a>
                                            <a class="ms-2" @click="removeMask(mask.id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 32 32">
                                                    <path d="M21.66,10.34a1,1,0,0,0-1.42,0L16,14.59l-4.24-4.25a1,1,0,0,0-1.42,1.42L14.59,16l-4.25,4.24a1,1,0,0,0,1.42,1.42L16,17.41l4.24,4.25a1,1,0,0,0,1.42-1.42L17.41,16l4.25-4.24A1,1,0,0,0,21.66,10.34Z"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="hasNoMasks">
                                        <td class="text-center">{{ translates.emptyList }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <EstateModal :id="getModalName()">
            <template v-slot:title>{{ translates.mask }} {{ modal.index }}</template>
            <template v-slot:body>
                <fieldset class="md-3">
                    <div class="form-group">
                        <label for="mask-coords" class="form-label">{{ translates.coords }} <sup class="text-danger">*</sup></label>
                        <textarea id="mask-coords"
                                  class="form-control"
                                  rows="5"
                                  v-model="modal.mask.coords"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ translates.binding }}</label>
                        <label class="form-label">
                            <input type="radio"
                                   :checked="typeIsEmpty(modal.mask.type)"
                                   @click="setModalMaskType(null)" />
                            {{ translates.noBinding }}
                        </label>
                        <label class="form-label">
                            <input type="radio"
                                   :checked="typeIsStep(modal.mask.type)"
                                   @click="setModalMaskType(data.nextStep)" />
                            {{ translates.nextStep }}
                        </label>
                        <label class="form-label">
                            <input type="radio"
                                   :checked="typeIsUrl(modal.mask.type)"
                                   @click="setModalMaskType('url')" />
                            {{ translates.link }}
                        </label>
                    </div>
                    <div v-if="modal.mask.pointer" class="form-group">
                        <label class="form-label">{{ translates.pointer }}</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" min="0" step="1" class="form-control" v-model="modal.mask.pointer[0]" />
                            </div>
                            <div class="col-6">
                                <input type="number" min="0" step="1" class="form-control" v-model="modal.mask.pointer[1]" />
                            </div>
                        </div>
                    </div>
                    <div v-if="typeIsStep(modal.mask.type)" class="form-group">
                        <label class="form-label">{{ translates.nextStepHeader }}</label>
                        <select class="form-control" v-model="modal.mask.element_id">
                            <option v-for="el of elements"
                                    :selected="el.maskId === modal.mask.id"
                                    :disabled="elementHasLink(el) && !elementHasMask(el, modal.mask.id)"
                                    :value="el.id">{{ el.title }}</option>
                        </select>
                    </div>
                    <div v-if="typeIsUrl(modal.mask.type)" class="form-group">
                        <label for="mask-url" class="form-label">{{ translates.link }}</label>
                        <input id="mask-url"
                               type="text"
                               maxlength="255"
                               placeholder="https://example.com"
                               class="form-control"
                               v-model="modal.mask.value" />
                    </div>
                </fieldset>
            </template>
            <template v-slot:footer>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal" @click="saveModal()">{{ translates.apply }}</button>
            </template>
        </EstateModal>
    </fieldset>
</template>

<script>
import EstateModal from "./EstateModal.vue";

const FILE_FIRST_ELEMENT = 0;

export default {
    name: "EstateVisualAngleMasks",
    components: {
        EstateModal
    },
    props: {
        angleId: {
            type: Number | String,
            required: true
        },
        masks: {
            type: Array,
            required: true
        },
        elements: {
            type: Array,
            required: true
        }
    },
    inject: ['translates', 'data'],
    data() {
        return {
            modal: {
                index: 0,
                mask: {}
            }
        };
    },
    created() {
        this.syncMaskToElements();
        this.$eventBus.$on('click-on-mask-path', this.showMaskOnList);
        this.$eventBus.$on('link-mask-to-element', this.linkMaskBySerialNumber);
    },
    computed: {
        hasNoData() {
            return !this.elements.length;
        },
        hasMasks() {
            return this.masks.length;
        },
        hasNoMasks() {
            return !this.hasMasks;
        },
        hasFreeMasks() {
            if (!this.hasMasks) {
                return false;
            }

            return this.masks.find((mask) => !mask.type);
        }
    },
    methods: {
        typeIsEmpty(type) {
            return !type;
        },
        typeIsUrl(type) {
            return type === 'url';
        },
        typeIsStep(type) {
            return type && !this.typeIsUrl(type);
        },
        elementCanLink(el) {
            return !el.maskId && this.hasFreeMasks;
        },
        elementHasLink(el) {
            return el.maskId || false;
        },
        elementHasMask(el, maskId) {
            return el.maskId === maskId;
        },
        getPointerPosition(mask, pos) {
            return Object.prototype.hasOwnProperty.call(mask, 'pointer') ? mask.pointer[pos] ?? 0 : 0;
        },
        toggleDropdown(event) {
            const el = event.target.closest('.dropdown-toggle');

            if (!el) {
                return;
            }

            window.Bootstrap.Dropdown.getOrCreateInstance(el)?.toggle();
        },
        openModal(mask, index) {
            this.modal = Object.assign(
                {},
                {
                    index: index + 1,
                    mask: Object.assign({}, mask)
                }
            );

            window.Bootstrap.Modal.getOrCreateInstance(`#${this.getModalName()}`)?.show();
        },
        getModalName() {
            return `maskEdit-${this.angleId}`;
        },
        saveModal() {
            let mask = this.masks.find(mask => mask.id === this.modal.mask.id);

            if (mask) {
                mask = Object.assign(mask, this.modal.mask);
                this.syncMaskToElements();
            }

            this.$emit('mask-updated', mask.id);
        },
        setModalMaskType(type) {
            this.modal.mask.type = type;
            this.modal.mask.value = null;
            this.modal.mask.element_id = 0;
            this.modal.mask.elementTitle = null;
        },
        removeMask(id) {
            if (confirm(this.translates.confirmDeleteMask)) {
                this.detachMaskFromElementByMask(id);
                this.$emit('mask-remove', id);
            }
        },
        removeMaskLinkByElement(el) {
            this.detachMaskFromElementByElement(el);

            const mask = this.masks.find((mask) => mask.type === this.data.nextStep && mask.element_id === el.id);

            if (mask) {
                this.$emit('mask-remove-link', mask.id);
            }
        },
        removeMaskLink(mask) {
            if (!this.typeIsUrl(mask.type) && mask.type === this.data.nextStep) {
                for (let el of this.elements) {
                    if (el.id === mask.element_id) {
                        this.detachMaskFromElementByElement(el);
                        break;
                    }
                }
            }

            this.$emit('mask-remove-link', mask.id);
        },
        detachMaskFromElementByMask(maskId) {
            for (let el of this.elements) {
                if (el.hasOwnProperty('maskId') && el.maskId === maskId) {
                    this.detachMaskFromElementByElement(el);
                    break;
                }
            }
        },
        detachMaskFromElementByElement(el) {
            el = Object.assign(el, {maskId: 0, maskTitle: null});
        },
        syncMaskToElements() {
            let index = 1;
            for (let el of this.elements) {
                el.maskId = 0;
                el.maskTitle = null;
            }
            for (let mask of this.masks) {
                for (let el of this.elements) {
                    if (!el.hasOwnProperty('maskId')) {
                        this.detachMaskFromElementByElement(el);
                    }

                    if (mask.type === this.data.nextStep && el.id === mask.element_id) {
                        mask = Object.assign(mask, {elementTitle: el.title});
                        el = Object.assign(el, {maskId: mask.id, maskTitle: index});
                    }
                }
                index++;
            }
            this.$eventBus.$emit('sync-masks-list', this.angleId);
        },
        linkElementToMask(target, el, mask, index) {
            const wrap = target.closest('ul');

            wrap.classList.remove('show');
            wrap.style = {};

            this._linkElToMask(el, mask, index);
        },
        _linkElToMask(el, mask, index)
        {
            el = Object.assign(el, {maskId: mask.id, maskTitle: index});

            this.$emit(
                'mask-set-link',
                {
                    id: mask.id,
                    type: this.data.nextStep,
                    value: null,
                    element_id: el.id,
                    elementTitle: el.title
                }
            );
        },
        showMaskOnList(angleId, maskId) {
            if (this.angleId !== angleId) {
                return;
            }

            let mask = this.masks.find(mask => mask.id === maskId);

            if (!mask) {
                return;
            }

            mask.highlight = true;
            this.$forceUpdate();

            setTimeout(() => {
                mask.highlight = false;
                this.$forceUpdate();
            }, 1000);
        },
        linkMaskBySerialNumber(angleId, maskId, serialNumber) {
            if (this.angleId !== angleId) {
                return;
            }

            let index = null;
            let mask = null;

            for (const i in this.masks) {
                if (this.masks[i].id === maskId) {
                    mask = this.masks[i];
                    index = i;
                    break;
                }
            }

            if (!mask) {
                return;
            }

            let el = null;

            for (const i in this.elements) {
                if (!this.elements[i].hasOwnProperty('serialNumber')) {
                    continue;
                }
                if (this.elements[i].serialNumber === serialNumber) {
                    el = this.elements[i];
                    break;
                }
            }

            if (!el) {
                return;
            }

            this._linkElToMask(el, mask, index);
        }
    }
}
</script>
