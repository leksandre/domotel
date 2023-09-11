<fieldset class="mb-3">
    <div class="bg-white rounded shadow-sm p-2 py-4 d-flex flex-column">
        <div data-controller="estate-visual-vue"
             data-url="{{ $url ?? '' }}"
             data-translates="{!! $translates ?? '{}' !!}"
             data-storage-url="{{ $route ?? '' }}"
             data-storage-disk="{{ $storageDisk ?? 'public' }}"
             data-groups="{{ $groups ?? '' }}"></div>
    </div>
</fieldset>
