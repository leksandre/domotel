<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Orders;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder;

final class PriceTotal extends AbstractOrder
{
    protected const NAME = 'price';
    protected const TITLE_ASC = 'kelnik-estate-search::front.sort.price.asc';
    protected const TITLE_DESC = 'kelnik-estate-search::front.sort.price.desc';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.orders.price';

    public function getDataOrder(): Collection
    {
        $order = $this->getOrderFromRequest();

        if ($order && $order !== self::NAME) {
            return new Collection();
        }

        return new Collection([
            'status.priority' => self::DIRECTION_ASC,
            'price_total' => $this->getDirectionFromRequest()
        ]);
    }
}
