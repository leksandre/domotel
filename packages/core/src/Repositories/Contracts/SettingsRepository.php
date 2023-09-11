<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Core\Models\Setting;

interface SettingsRepository
{
    /** Get all module settings*/
    public function getAllByModule(string $moduleName): Collection;

    /**
     * Get collection of module settings filtered by name.
     * If setting name is array then returns collection, on setting name is string then returns setting model.
     */
    public function get(string $moduleName, string|array $settingName): null|Setting|Collection;

    /** Store setting */
    public function set(Setting $setting): bool;
}
