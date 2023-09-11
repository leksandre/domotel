<div class="pdf-section pdf-main">
    <div class="pdf-section__left">
        <div class="pdf-main__plan">
            @if($hasPopWidget)
                <img src="{!! $element->planoplan->widget->planTop() !!}" alt="{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.plan') }}">
            @else
                <img src="{!! $element->imagePlan?->url() ?? $element->imagePlanDefault !!}" alt="{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.plan') }}">
            @endif
        </div>
    </div>
    <div class="pdf-section__right">
        <h2 class="pdf-main__heading">{{ $title }}</h2>
        <div class="pdf-main__prices">
            @if(!$priceVisible && $element->status->additional_text)
                <div class="pdf-main__price">{{ $element->status->additional_text }}</div>
            @elseif($priceVisible && $element->price_sale)
                <div class="pdf-main__price pdf-main__price_action_price">{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.price', ['value' => number_format($element->price_sale, 0, ',', ' ')]) }}</div>
                <div class="pdf-main__action-wrapper">
                    <div class="pdf-main__action">
                        <span class="pdf-main__action-value">-{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.price', ['value' => number_format($element->price_total - $element->price_sale, 0, ',', ' ')]) }}</span>
                    </div>
                    <div class="pdf-main__basic-price">{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.price', ['value' => number_format($element->price_total, 0, ',', ' ')]) }}</div>
                </div>
            @elseif($priceVisible)
                <div class="pdf-main__price pdf-main__price_action_price">{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.price', ['value' => number_format($element->price_total, 0, ',', ' ')]) }}</div>
            @endif
        </div>
        <ul class="pdf-main__list pdf-main__list_theme_dashed">
            <li class="pdf-main__list-item">
                <div class="pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.areaTotal') }}</div>
                <div class="pdf-main__list-value">{{ trans('kelnik-estate::front.components.premisesCard.pdf.properties.area', ['value' => $element->area_total]) }}</div>
            </li>
            @if($hasBuilding)
                <li class="pdf-main__list-item">
                    <div class="pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.building') }}</div>
                    <div class="pdf-main__list-value">{{ $element->floor->building->title }}</div>
                </li>
            @endif
            @if($hasSection)
                <li class="pdf-main__list-item">
                    <div class="pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.section') }}</div>
                    <div class="pdf-main__list-value">{{ $element->section->title }}</div>
                </li>
            @endif
            @if($hasFloor)
                <li class="pdf-main__list-item">
                    <div class="pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.floor') }}</div>
                    <div class="pdf-main__list-value">{{ $element->floor->title }}@if($element->floor_max) {{ trans('kelnik-estate::front.components.premisesCard.pdf.maxFloor', ['value' => $element->floor_max]) }}@endif</div>
                </li>
            @endif
            @if($hasBuilding && $hasCompletion)
                <li class="pdf-main__list-item">
                    <div class="pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.completion') }}</div>
                    <div class="pdf-main__list-value">{{ $element->floor->building->completion->title }}</div>
                </li>
            @endif
        </ul>
        @if($element->relationLoaded('features') && $element->features->isNotEmpty())
            <div class="pdf-main__specials">
                <ul class="pdf-main__list pdf-main__list_theme_dotted">
                    <li class="pdf-main__list-item pdf-main__list-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.features') }}</li>
                    @foreach($element->features as $feature)
                        <li class="pdf-main__list-item">{{ $feature->full_title }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@if($hasPopWidget)
    <section class="pdf-section pdf-planoplan">
        @if($has3dPlan)
            <div class="pdf-section__left">
                <div class="pdf-planoplan__plan">
                    <img src="{!! $element->planoplan->widget->plan3d() !!}" alt="{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.plan3D') }}">
                </div>
            </div>
        @endif
        <div class="pdf-section__right">
            <h3>{!! trans('kelnik-estate::front.components.premisesCard.pdf.planoplan.title') !!}</h3>
            <p class="pdf__text pdf-planoplan__text">{!! trans('kelnik-estate::front.components.premisesCard.pdf.planoplan.text') !!}</p>
            <div class="pdf-planoplan__app">
                <div class="pdf-planoplan__qr"><img src="{!! $element->planoplan->widget->qrCodeLink() !!}" alt="QR code"></div>
                <ul class="pdf-planoplan__list">
                    <li><img src="/images/pdf/store-apple.jpg" alt="Google Play"></li>
                    <li><img src="/images/pdf/store-google.jpg" alt="Appstore"></li>
                </ul>
            </div>
        </div>
    </section>
@endif
@if($hasFloorPlan || $hasBuildingPlan)
    <section class="pdf-section">
        @if($hasFloorPlan)
            <div class="pdf-section__left">
                <div class="pdf-plan">
                    <img src="{!! $element->imageOnFloor->url() !!}" alt="{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.onFloor') }}">
                    <div class="pdf-plan__text">{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.onFloor') }}</div>
                </div>
            </div>
        @endif
        @if($hasBuildingPlan)
            <div class="pdf-section__right">
                <div class="pdf-plan">
                    <img src="{!! $element->floor->building->complexPlan->url() !!}" alt="{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.onBuildingPlan') }}">
                    <div class="pdf-plan__text">{{ trans('kelnik-estate::front.components.premisesCard.pdf.plan.onBuildingPlan') }}</div>
                </div>
            </div>
        @endif
    </section>
@endif
