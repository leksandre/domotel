<div class="flat-card__tab-container">
    <ul class="flat-card__tab-wrapper j-tabs">
        @if($hasPop)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item is-active" data-tab="flat_widget">{{ trans('kelnik-estate::front.components.premisesCard.plan.planoplan') }}</button></li>
        @elseif($hasPlan)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item is-active" data-tab="flat_plan">{{ trans('kelnik-estate::front.components.premisesCard.plan.plan') }}</button></li>
        @elseif($has3dPlan)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item is-active" data-tab="flat_plan3d">{{ trans('kelnik-estate::front.components.premisesCard.plan.plan3d') }}</button></li>
        @else
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item is-active" data-tab="flat_plan">{{ trans('kelnik-estate::front.components.premisesCard.plan.plan') }}</button></li>
        @endif
        @if($hasGallery)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item" data-tab="flat_slider">{{ trans('kelnik-estate::front.components.premisesCard.plan.gallery') }}</button></li>
        @endif
        @if($hasFloorPlan)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item" data-tab="flat_floor">{{ trans('kelnik-estate::front.components.premisesCard.plan.onFloor') }}</button></li>
        @endif
        @if($hasBuildingPlan)
            <li class="flat-card__tab"><button class="flat-card__tab-button j-tabs__item" data-tab="flat_general">{{ trans('kelnik-estate::front.components.premisesCard.plan.onBuildingPlan') }}</button></li>
        @endif
    </ul>
</div>
