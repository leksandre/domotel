<fieldset class="mb-3">
    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        <div class="form-group">
            <div class="kelnik-platform-title">@lang('kelnik-menu::admin.items.title')</div>
        </div>
        <div class="form-group">
            <div data-controller="menu-strict-vue"
                 data-translates="{!! $translates ?? '{}' !!}"
                 data-content="{!! $content ?? '{}' !!}"
                 data-modal="{{ $modalId ?? 'menu-item' }}"
                 data-route-list="{{ route(resolve(\Kelnik\Core\Services\Contracts\CoreService::class)->getFullRouteName('page.components.list')) }}"
                 data-route-url="{{ route(resolve(\Kelnik\Core\Services\Contracts\CoreService::class)->getFullRouteName('page.components.url')) }}"
            ></div>
        </div>
    </div>
</fieldset>
