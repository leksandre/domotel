<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface PremisesCardToPdf
{
    public function __construct(string|int $primary, string $routeName, int|string $pageComponentKey, array $data);

    public function getLink(): ?string;

    public function send(): Response;
}
