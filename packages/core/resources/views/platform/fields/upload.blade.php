@component($typeForm, get_defined_vars())
    <div
        data-controller="kelnik-upload"
        data-kelnik-upload-storage="{{$storage ?? 'public'}}"
        data-kelnik-upload-name="{{$name}}"
        data-kelnik-upload-id="dropzone-{{$id}}"
        data-kelnik-upload-data='@json($value)'
        data-kelnik-upload-groups="{{ $attributes['groups'] ?? ''}}"
        data-kelnik-upload-multiple="{{ $attributes['multiple']}}"
        data-kelnik-upload-parallel-uploads="{{ $parallelUploads }}"
        data-kelnik-upload-max-file-size="{{$maxFileSize }}"
        data-kelnik-upload-max-files="{{$maxFiles}}"
        data-kelnik-upload-timeout="{{$timeOut}}"
        data-kelnik-upload-accepted-files="{{ $acceptedFiles }}"
        data-kelnik-upload-resize-quality="{{ $resizeQuality }}"
        data-kelnik-upload-resize-width="{{ $resizeWidth }}"
        data-kelnik-upload-is-media-library="{{ $media }}"
        data-kelnik-upload-close-on-add="{{ $closeOnAdd }}"
        data-kelnik-upload-resize-height="{{ $resizeHeight }}"
        data-kelnik-upload-path="{{ $attributes['path'] ?? '' }}"
        data-kelnik-upload-chunking="{{ $chunking }}"
        data-kelnik-upload-chunk-size="{{ $chunkSize }}"
    >
        <div id="dropzone-{{$id}}" class="dropzone-wrapper">
            <div class="fallback">
                <input type="file" value="" multiple/>
            </div>
            <div class="visual-dropzone sortable-dropzone dropzone-previews">
                <div class="dz-message dz-preview dz-processing dz-image-preview">
                    <div class="bg-light d-flex justify-content-center align-items-center border r-2x" style="min-height: 112px;">
                        <div class="px-2 py-4">
                            <x-orchid-icon path="bs.cloud-arrow-up" class="h3"/>
                            <small class="text-muted d-block mt-1">{{__('Upload file')}}</small>
                        </div>
                    </div>
                </div>

                @if($media)
                    <div class="dz-message dz-preview dz-processing dz-image-preview" data-action="click->kelnik-upload#openMedia">
                        <div class="bg-light d-flex justify-content-center align-items-center border r-2x" style="min-height: 112px;">
                            <div class="px-2 py-4">
                                <x-orchid-icon path="bs.collection" class="h3"/>
                                <small class="text-muted d-block mt-1">{{__('Media catalog')}}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="attachment modal fade center-scale" tabindex="-1" role="dialog" aria-hidden="false">
                <div class="modal-dialog modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title text-black fw-light">
                                {{__('File Information')}}
                                <small class="text-muted d-block">{{__('Information to display')}}</small>
                            </h4>
                            <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group">
                                <label>{{__('System name')}}</label>
                                <input type="text" class="form-control" data-kelnik-upload-target="name" readonly maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Display name') }}</label>
                                <input type="text" class="form-control" data-kelnik-upload-target="original" maxlength="255" placeholder="{{ __('Display name') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Alternative text') }}</label>
                                <input type="text" class="form-control" data-kelnik-upload-target="alt" maxlength="255" placeholder="{{  __('Alternative text')  }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Description') }}</label>
                                <textarea class="form-control no-resize"
                                          data-kelnik-upload-target="description"
                                          placeholder="{{ __('Description') }}"
                                          maxlength="255"
                                          rows="3"></textarea>
                            </div>


                            @if($visibility === 'public')
                                <div class="form-group">
                                    <a href="#" data-action="click->kelnik-upload#openLink">
                                        <small>
                                            <x-orchid-icon path="bs.share" class="me-2"/>
                                            {{ __('Link to file') }}
                                        </small>
                                    </a>
                                </div>
                            @endif

                        </div>
                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-link">
                                <span>{{__('Close')}}</span>
                            </button>
                            <button type="button" data-action="click->kelnik-upload#save" class="btn btn-default">{{__('Apply')}}</button>
                        </div>
                    </div>
                </div>
            </div>

            @if($media)
                <div class="media modal fade enter-scale disable-scroll" tabindex="-1" role="dialog" aria-hidden="false">
                    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down slide-up">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-black fw-light">
                                    {{__('Media Library')}}
                                    <small class="text-muted d-block">{{__('Previously uploaded files')}}</small>
                                </h4>
                                <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('Search file')}}</label>
                                            <input type="search"
                                                   data-kelnik-upload-target="search"
                                                   data-action="keydown->kelnik-upload#resetPage keydown->kelnik-upload#loadMedia"
                                                   class="form-control"
                                                   placeholder="{{ __('Search...') }}"
                                            >
                                        </div>

                                        <div class="media-loader spinner-border" role="status">
                                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                                        </div>
                                        <div class="row media-results m-0"></div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-link d-block w-100"
                                                    data-kelnik-upload-target="loadmore"
                                                    data-action="click->kelnik-upload#loadMore">{{ __('Load more') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <template id="dropzone-{{$id}}-media">
                    <div class="col-4 col-sm-3 my-3 position-relative media-item">
                        <div data-action="click->kelnik-upload#addFile" data-key="{index}">
                            <img src="{element.url}" class="rounded mw-100" style="height: 50px;width: 100%;object-fit: cover;">
                            <p class="text-ellipsis small text-muted mt-1 mb-0" title="{element.original_name}">{element.original_name}</p>
                        </div>
                    </div>
                </template>
            @endif

            <template id="dropzone-{{$id}}-remove-button">
                <a href="javascript:;" class="btn-remove">&times;</a>
            </template>

            <template id="dropzone-{{$id}}-edit-button">
                <a href="javascript:;" class="btn-edit"><x-orchid-icon path="bs.card-text" class="mb-1"/></a>
            </template>
        </div>
    </div>
@endcomponent
