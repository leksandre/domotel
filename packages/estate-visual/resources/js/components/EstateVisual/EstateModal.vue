<template>
    <div class="modal fade" :id="`${id}`" tabindex="-1" :aria-labelledby="`label-${id}`" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-black fw-light" :id="`label-${id}`">
                        <slot name="title"></slot>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body layout-wrapper">
                    <fieldset class="md-3">
                        <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
                            <slot name="body"></slot>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ translates.close }}</button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EstateModal",
    inject: ['translates'],
    props: {
        id: {
            type: String,
            required: true
        }
    }
}
</script>
