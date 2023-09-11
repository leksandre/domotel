<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Components;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\EstateModelProxy;
use Kelnik\EstateImport\Platform\Components\Contracts\PlatformComponent;
use Kelnik\EstateImport\Platform\Services\Contracts\ImportPlatformService;
use Orchid\Screen\Actions\Link;

final class StateInfo extends PlatformComponent implements KelnikComponentAlias
{
    private History $history;
    private ImportPlatformService $service;

    public function __construct(History $value)
    {
        $this->history = $value;
        $this->service = resolve(ImportPlatformService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-import-platform-state';
    }

    public function getLogLinks(): string
    {
        if ($this->history->state->isNew()) {
            return '';
        }

        $dates = [];
        $res = '';

        foreach ($this->history->result as $stateName => $stateData) {
            foreach (['start', 'finish'] as $period) {
                if (!isset($stateData['time'][$period])) {
                    continue;
                }

                $dateTime = Carbon::createFromTimestamp($stateData['time'][$period]);
                $key = $dateTime->format('y-m-d');

                if (!isset($dates[$key])) {
                    $dates[$key] = $dateTime;
                    $res .= $this->getLogLink($dateTime);
                }
            }
        }

        return $res;
    }

    private function getLogLink(DateTimeInterface $dateTime): string
    {
        return Link::make(trans(
            'kelnik-estate-import::admin.history.logLink',
            ['date' => $dateTime->format('d.m.Y')]
        ))
            ->icon('bs.file-earmark')
            ->class('d-block pt-2 text-secondary')
            ->href($this->service->getLogLink($dateTime))
            ->toHtml();
    }

    public function render(): View
    {
        $res = Arr::get($this->history->result, $this->history->state->value, []);
        $stat = Arr::get($res, 'stat', []);

        if ($stat) {
            $stat = array_filter(
                $stat,
                fn($className) => is_a($className, EstateModelProxy::class, true),
                ARRAY_FILTER_USE_KEY
            );

            /**
             * @var EstateModelProxy $a
             * @var EstateModelProxy $b
             */
            uksort($stat, static fn($a, $b) => $a::getSort() <=> $b::getSort());
        }

        return view(
            'kelnik-estate-import::platform.components.state-info',
            [
                'history' => $this->history,
                'stateClass' => $this->getStateClass($this->history->state->value),
                'batch' => $this->history->batch_id ? Bus::findBatch($this->history->batch_id) : false,
                'res' => $res,
                'stat' => $stat,
                'message' => Str::limit(Arr::get($res, 'message'), 400),
                'filePath' => Arr::get($res, 'file')
            ]
        );
    }
}
