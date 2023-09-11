<template>
    <div v-if="error"
         class="tree">
        <p class="tree__error">
            {{ translates.errorHeader }}: {{ error }}
        </p>
    </div>
    <div v-else
         class="tree">
        <vue-tree-list
            @click="onClick"
            @delete-node="onDel"
            @add-node="onAddNode"
            :model="data"
            v-bind:default-expanded="true"
            class="mb-3"
        >
            <template v-slot:leafNameDisplay="slotProps">
                <span>
                  <input type="hidden" v-for="(paramKey, paramName) in modelParams" v-model="slotProps.model[paramKey.name]" :name="getInputName(slotProps.model, paramName)">
                  <input type="checkbox" v-model="slotProps.model.marked" :name="getInputName(slotProps.model, 'marked')" @click.stop checked="checked" class="d-none">
                  <input type="checkbox" v-model="slotProps.model.active" :name="getInputName(slotProps.model, 'active')" @click.stop checked="checked" class="mr-2">
                  {{ slotProps.model.name }} <span class="muted"> {{ slotProps.model.url }}</span>
                </span>
            </template>
            <span class="icon" slot="addTreeNodeIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor">
                    <path d="M16 0c-8.836 0-16 7.163-16 16s7.163 16 16 16c8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 30.032c-7.72 0-14-6.312-14-14.032s6.28-14 14-14 14 6.28 14 14-6.28 14.032-14 14.032zM23 15h-6v-6c0-0.552-0.448-1-1-1s-1 0.448-1 1v6h-6c-0.552 0-1 0.448-1 1s0.448 1 1 1h6v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6h6c0.552 0 1-0.448 1-1s-0.448-1-1-1z"></path>
                </svg>
            </span>
            <span class="icon" slot="editNodeIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor">
                    <path d="M30.133 1.552c-1.090-1.044-2.291-1.573-3.574-1.573-2.006 0-3.47 1.296-3.87 1.693-0.564 0.558-19.786 19.788-19.786 19.788-0.126 0.126-0.217 0.284-0.264 0.456-0.433 1.602-2.605 8.71-2.627 8.782-0.112 0.364-0.012 0.761 0.256 1.029 0.193 0.192 0.45 0.295 0.713 0.295 0.104 0 0.208-0.016 0.31-0.049 0.073-0.024 7.41-2.395 8.618-2.756 0.159-0.048 0.305-0.134 0.423-0.251 0.763-0.754 18.691-18.483 19.881-19.712 1.231-1.268 1.843-2.59 1.819-3.925-0.025-1.319-0.664-2.589-1.901-3.776zM22.37 4.87c0.509 0.123 1.711 0.527 2.938 1.765 1.24 1.251 1.575 2.681 1.638 3.007-3.932 3.912-12.983 12.867-16.551 16.396-0.329-0.767-0.862-1.692-1.719-2.555-1.046-1.054-2.111-1.649-2.932-1.984 3.531-3.532 12.753-12.757 16.625-16.628zM4.387 23.186c0.55 0.146 1.691 0.57 2.854 1.742 0.896 0.904 1.319 1.9 1.509 2.508-1.39 0.447-4.434 1.497-6.367 2.121 0.573-1.886 1.541-4.822 2.004-6.371zM28.763 7.824c-0.041 0.042-0.109 0.11-0.19 0.192-0.316-0.814-0.87-1.86-1.831-2.828-0.981-0.989-1.976-1.572-2.773-1.917 0.068-0.067 0.12-0.12 0.141-0.14 0.114-0.113 1.153-1.106 2.447-1.106 0.745 0 1.477 0.34 2.175 1.010 0.828 0.795 1.256 1.579 1.27 2.331 0.014 0.768-0.404 1.595-1.24 2.458z"></path>
                </svg>
            </span>
            <span class="icon mr-3" slot="delNodeIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor">
                    <path d="M28.025 4.97l-7.040 0v-2.727c0-1.266-1.032-2.265-2.298-2.265h-5.375c-1.267 0-2.297 0.999-2.297 2.265v2.727h-7.040c-0.552 0-1 0.448-1 1s0.448 1 1 1h1.375l2.32 23.122c0.097 1.082 1.019 1.931 2.098 1.931h12.462c1.079 0 2-0.849 2.096-1.921l2.322-23.133h1.375c0.552 0 1-0.448 1-1s-0.448-1-1-1zM13.015 2.243c0-0.163 0.133-0.297 0.297-0.297h5.374c0.164 0 0.298 0.133 0.298 0.297v2.727h-5.97zM22.337 29.913c-0.005 0.055-0.070 0.11-0.105 0.11h-12.463c-0.035 0-0.101-0.055-0.107-0.12l-2.301-22.933h17.279z"></path>
                </svg>
            </span>
        </vue-tree-list>
        <div class="form-group">
            <div class="btn-group">
                <a type="button"
                        class="btn btn-default m-0"
                        @click="addNode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor">
                        <path d="M16 0c-8.836 0-16 7.163-16 16s7.163 16 16 16c8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 30.032c-7.72 0-14-6.312-14-14.032s6.28-14 14-14 14 6.28 14 14-6.28 14.032-14 14.032zM23 15h-6v-6c0-0.552-0.448-1-1-1s-1 0.448-1 1v6h-6c-0.552 0-1 0.448-1 1s0.448 1 1 1h6v6c0 0.552 0.448 1 1 1s1-0.448 1-1v-6h6c0.552 0 1-0.448 1-1s-0.448-1-1-1z"></path>
                    </svg> {{ translates.addButton }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import { VueTreeList, Tree, TreeNode } from 'vue-tree-list';
import data from './mixins/data';
import tools from './mixins/tools';
import modal from './mixins/modal';

export default {
    mixins: [tools, modal],

    name: 'MenuTree',

    components: {
        VueTreeList
    },

    modal: null,
    modalForm: null,
    defaultAxiosConfig: null,

    data() {
        return Object.assign({data: new Tree([])}, data);
    },

    mounted() {
        if (this.$attrs.content) {
            try {
                this.content = JSON.parse(atob(this.$attrs.content));
                this.data = new Tree(this.content);
            } catch (error) {
                this.error = error;
            }
        }

        this.bindModalEvents();
    },

    methods: {
        onDel(node) {
            if (confirm(`${this.translates.deleteConfirm} ${node.name}?`)) {
                node.remove();
            }
        },

        onAddNode(params) {
            params.name = this.translates.newElement;
            Object.assign(params, this.defaultModel);

            this.fillModal(params);
            this.openModal();
        },

        onClick(params) {
            this.fillModal(params);
            this.openModal();
        },

        addNode() {
            const node = new TreeNode(Object.assign(
                {
                    name: this.translates.newElement
                },
                this.defaultModel
            ));

            if (!this.data.children) {
                this.data.children = [];
            }
            this.data.addChildren(node);

            this.fillModal(node);
            this.openModal();
        },

        _findNode(nodeId, tree) {
            if ((tree.id || undefined) === nodeId) {
                return tree;
            }

            if (typeof tree.children !== 'object' || tree.children === null || !tree.children.length) {
                return false;
            }

            for (let node in tree.children) {
                let res = this._findNode(nodeId, tree.children[node]);
                if (res !== false) {
                    return res;
                }
            }

            return false;
        }
    }
}
</script>

<style lang="scss">
    @import "MenuTree";
</style>
