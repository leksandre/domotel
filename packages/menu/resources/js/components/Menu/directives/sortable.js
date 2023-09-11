import Sortable from 'sortablejs/modular/sortable.core.esm.js';

const SortableDirective = {
    inserted(el, binding, vnode) {
        const options = binding.value;

        options.onUpdate = (elem) => {
            return vnode.data.on?.sorted(elem);
        };

        Sortable.create(el, binding.value);
    }
};

export default SortableDirective;
