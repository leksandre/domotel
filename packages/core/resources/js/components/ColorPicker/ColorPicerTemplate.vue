<template>
    <div v-click-outside="hidePicker">
        <label v-if="label"
               :for="id"
               class="form-label">{{ label }}</label>
        <div class="input-group color-picker flex">
            <div class="color-picker__wrapper">
                <chrome-picker v-if="picker === 'chrome'"
                               v-show="displayPicker"
                               :value="colors"
                               :disable-fields="true"
                               :disable-alpha="true"
                               class="vc-right"
                               @input="updateFromPicker" />
                <sketch-picker v-if="picker === 'sketch'"
                               v-show="displayPicker"
                               :value="colors"
                               :disable-fields="true"
                               :disable-alpha="true"
                               class="vc-right"
                               @input="updateFromPicker" />
            </div>
            <div class="color-picker__container">
                <div class="color-picker__color"
                     :style="colorBox()"
                     @click="showPicker"></div>
            </div>
            <input ref="input"
                   :id="id"
                   :name="name"
                   type="text"
                   v-bind="$attrs"
                   class="form-control color-input"
                   @focus="showPicker"
                   @input="updateFromInput($event.target.value)"
                   @change="hidePicker"
                   placeholder="Выберите цвет..."
            />
            <button class="color-picker__reset-button"
                    :disabled="colorDefault === colorValue"
                    :title="colorDefault === colorValue ? '' : 'Вернуть настройку по умолчанию'"
                    @click="updateFromInput(colorDefault)">
                <IconReset />
            </button>
        </div>
    </div>
</template>

<script>
import { Chrome, Sketch } from 'vue-color';
import ClickOutside from 'vue-click-outside';
import IconReset from '../Icons/icon-reset.vue';

export default {
    name: 'ColorPickerTemplate',

    components: {
        'chrome-picker': Chrome,
        'sketch-picker': Sketch,
        IconReset
    },

    directives: {
        ClickOutside
    },

    inheritAttrs: false,

    props: {
        id: {
            type: String,
            default: () => ''
        },
        name: {
            type: String,
            default: () => ''
        },
        color: {
            type: String,
            default: () => '',
        },
        label: {
            type: String,
            default: () => '',
        },
        picker:{
            type: String,
            default: () => 'chrome',
            validator: value => {
                return ['chrome', 'sketch'].indexOf(value) !== -1
            }
        },
        colorDefault: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            colors: {
                hex: '#000000',
            },
            colorValue: '',
            displayPicker: false,
            defaultColor: '#FF0000'
        }
    },

    watch: {
        colorValue(val) {
            if(val) {
                this.updateColors(val);
                this.$emit('input', val);
            }
        }
    },

    mounted() {
        this.setColor(this.color || '#000000');
    },

    methods: {
        colorBox() {
            return `background-color: ${this.colorValue};`;
        },

        setColor(color) {
            this.updateColors(color);
            this.colorValue = color;
        },

        updateColors(color) {
            if (color.slice(0, 1) === '#') {
                this.colors = {
                    hex: color
                };
            }
            else if(color.slice(0, 4) === 'rgba') {
                let rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
                    hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);
                this.colors = {
                    hex: hex,
                    a: rgba[3],
                }
            }
        },

        showPicker() {
            if (!this.displayPicker) {
                this.displayPicker = true;
            }
        },

        hidePicker() {
            if (this.displayPicker) {
                this.displayPicker = false;
            }
        },

        togglePicker() {
            this.displayPicker ? this.hidePicker() : this.showPicker();
        },

        updateFromInput(value) {
            if (/^#([0-9A-F]{3}){1,2}$/i.test(value)) {
                this.colorValue = value;
            }
        },

        updateFromPicker(color) {
            this.colors = color;
            this.colorValue = color.hex;
        }
    }
}
</script>

<style lang="scss">
    @import "ColorPicker";
</style>
