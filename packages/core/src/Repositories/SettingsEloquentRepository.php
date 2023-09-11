<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Core\Events\SettingUpdated;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Repositories\Contracts\BaseEloquentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;

final class SettingsEloquentRepository extends BaseEloquentRepository implements SettingsRepository
{
    /** @var Setting $model */
    protected $model = Setting::class;

    public function getAllByModule(string $moduleName): Collection
    {
        return $this->model::query()->where('module', $moduleName)->get();
    }

    public function get(string $moduleName, string|array $settingName): null|Setting|Collection
    {
        $nameIsArray = true;

        if (!is_array($settingName)) {
            $settingName = [$settingName];
            $nameIsArray = false;
        }

        $res = $this->model::query()
            ->where('module', $moduleName)
            ->whereIn('name', $settingName);

        return $nameIsArray ? $res->get() : $res->first();
    }

    public function set(Setting $setting): bool
    {
        $res = $setting->exists
                ? Setting::query()
                    ->where('module', $setting->module)
                    ->where('name', $setting->name)
                    ->limit(1)
                    ->update(['value' => $setting->value]) > 0
                : Setting::create($setting->attributesToArray())->exists;

        if ($res) {
            SettingUpdated::dispatch($setting);
        }

        return $res;
    }
}
