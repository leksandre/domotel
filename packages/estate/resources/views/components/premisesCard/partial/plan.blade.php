<div class="flat__card">
    <div class="flat__tabs">
        <div class="flat__tabs-content-wrapper j-tabs-content">
            @if($hasPop)
                <div class="flat__tabs-content j-tabs-content__item" data-tab="flat_widget">
                    <div class="flat__plan">
                        <div class="flat__widget j-flat__planoplan-widget" id="planoplan" data-planoplan="{!! $planoplanData !!}"></div>
                    </div>
                </div>
            @elseif($hasPlan)
                <div class="flat__tabs-content j-tabs-content__item is-active" data-tab="flat_plan">
                    <div class="flat__plan">
                        <div class="flat__image">
                            <div class="flat__controls">
                                <button class="flat__fullscreen-button j-popup" data-gallery="true" data-slider="1" data-src="{!! $element->imagePlan->url() !!}" data-alt="{{ $title }}" data-caption="{{ $title }}" aria-label="{{ trans('kelnik-estate::front.components.premisesCard.plan.openInFullScreen') }}">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($element->imagePlanPicture))
                                {!! $element->imagePlanPicture !!}
                            @else
                                <img src="{!! $element->imagePlan->url() !!}" alt="plan">
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($has3dPlan)
                <div class="flat__tabs-content j-tabs-content__item" data-tab="flat_plan3d">
                    <div class="flat__plan">
                        <div class="flat__image">
                            <div class="flat__controls">
                                <button class="flat__fullscreen-button j-popup" data-gallery="true" data-slider="2" data-src="{!! $element->image3D->url() !!}" data-alt="{{ $title }}" data-caption="{{ $title }}" aria-label="{{ trans('kelnik-estate::front.components.premisesCard.plan.openInFullScreen') }}">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($element->image3dPicture))
                                {!! $element->image3dPicture !!}
                            @else
                                <img src="{!! $element->image3D->url() !!}" alt="plan">
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="flat__tabs-content j-tabs-content__item is-active" data-tab="flat_plan">
                    <div class="flat__plan">
                        <div class="flat__image">
                            <img src="{!! $element->imagePlanDefault !!}" alt="plan">
                        </div>
                    </div>
                </div>
            @endif
            @if($hasGallery)
                <div class="flat__tabs-content j-tabs-content__item is-active" data-tab="flat_slider">
                    <div class="flat__plan">
                        <div class="flat__slider">
                            <div class="slider slider_theme_flat j-slider-flat">
                                <div class="slider__wrap j-slides">
                                    @foreach($element->gallery as $slide)
                                        <div class="slider-flat">
                                            <div class="slider-flat__inner">
                                                @if(!empty($slide->picture))
                                                    {!! $slide->picture !!}
                                                @else
                                                    <img src="{!! $slide->url() !!}" alt="{{ $title }}">
                                                @endif
                                            </div>
                                            <div class="flat__controls">
                                                <button class="flat__fullscreen-button j-popup-slider" data-gallery="true" data-slider="0" data-src="{!! $slide->url() !!}" data-alt="{{ $title }}" data-caption="{{ $title }}" aria-label="{{ trans('kelnik-estate::front.components.premisesCard.plan.openInFullScreen') }}">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($hasFloorPlan)
                <div class="flat__tabs-content j-tabs-content__item" data-tab="flat_floor">
                    <div class="flat__plan">
                        <div class="flat__image">
                            <div class="flat__controls">
                                <button class="flat__fullscreen-button j-popup" data-gallery="true" data-slider="3" data-src="{!! $element->imageOnFloor->url() !!}" data-alt="{{ $title }}" data-caption="{{ $title }}" aria-label="{{ trans('kelnik-estate::front.components.premisesCard.plan.openInFullScreen') }}">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($element->imageOnFloorPicture))
                                {!! $element->imageOnFloorPicture !!}
                            @else
                                <img src="{!! $element->imageOnFloor->url() !!}" alt="plan">
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($hasBuildingPlan)
                <div class="flat__tabs-content j-tabs-content__item" data-tab="flat_general">
                    <div class="flat__plan">
                        <div class="flat__image">
                            <div class="flat__controls">
                                <button class="flat__fullscreen-button j-popup" data-gallery="true" data-slider="4" data-src="{!! $element->floor->building->complexPlan->url() !!}" data-alt="{{ $title }}" data-caption="{{ $title }}" aria-label="{{ trans('kelnik-estate::front.components.premisesCard.plan.openInFullScreen') }}">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                    </svg>
                                </button>
                            </div>
                            @if(!empty($element->imageBuildingPlanPicture))
                                {!! $element->imageBuildingPlanPicture !!}
                            @else
                                <img src="{!! $element->floor->building->complexPlan->url() !!}" alt="plan">
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
