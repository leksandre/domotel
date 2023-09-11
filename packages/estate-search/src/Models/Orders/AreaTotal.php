<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Orders;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder;

final class AreaTotal extends AbstractOrder
{
    protected const NAME = 'area';
    protected const TITLE_ASC = 'kelnik-estate-search::front.sort.area.asc';
    protected const TITLE_DESC = 'kelnik-estate-search::front.sort.area.desc';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.orders.area';

    public function getDataOrder(): Collection
    {
        $order = $this->getOrderFromRequest();

        if ($order !== self::NAME) {
            return new Collection();
        }

        return new Collection([
            'status.priority' => self::DIRECTION_ASC,
            'area_total' => $this->getDirectionFromRequest()
        ]);
    }
}
