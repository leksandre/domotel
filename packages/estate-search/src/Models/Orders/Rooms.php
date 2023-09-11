<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Orders;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder;

final class Rooms extends AbstractOrder
{
    protected const NAME = 'rooms';
    protected const TITLE_ASC = 'kelnik-estate-search::front.sort.rooms.asc';
    protected const TITLE_DESC = 'kelnik-estate-search::front.sort.rooms.desc';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.orders.rooms';

    public function getDataOrder(): Collection
    {
        $order = $this->getOrderFromRequest();

        if ($order && $order !== self::NAME) {
            return new Collection();
        }

        return new Collection([
            'status.priority' => self::DIRECTION_ASC,
            'type.rooms' => $this->getDirectionFromRequest()
        ]);
    }
}
