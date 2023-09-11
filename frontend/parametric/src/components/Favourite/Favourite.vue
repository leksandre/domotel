<!-- только иконка и позиционирование (без какого-либо функционала) -->
<template>
    <div>
        <PremisesFavouriteIcon
            class-name="icon_stroke_color icon_color_dark"
        />
    </div>
</template>

<script>
import PremisesFavouriteIcon from '../Icons/PremisesFavouriteIcon';

export default {
    name: 'Favourite-component',

    components: {
        PremisesFavouriteIcon
    }
};
</script>
