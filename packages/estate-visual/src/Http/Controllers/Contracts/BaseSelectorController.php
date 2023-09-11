<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Controllers\Contracts;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use InvalidArgumentException;
use Kelnik\Core\Http\Controllers\BaseApiController;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Services\Contracts\SearchConfigFactory;

abstract class BaseSelectorController extends BaseApiController
{
    public const PARAM_REQUEST_STEP = 'step';

    public function __construct()
    {
        $this->middleware([EncryptCookies::class, StartSession::class]);
    }

    protected function initSearchConfig(int|string $cid): SearchConfig
    {
        if (!strlen($cid) || !$config = resolve(SearchConfigFactory::class)->make($cid)) {
            throw new InvalidArgumentException();
        }

        return $config;
    }

    protected function getCacheId(int|string $cid): string
    {
        return 'estateVisual_config_' . $cid;
    }
}
