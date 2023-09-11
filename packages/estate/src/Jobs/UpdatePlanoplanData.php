<?php

declare(strict_types=1);

namespace Kelnik\Estate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

final class UpdatePlanoplanData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 3600; // 1h

    public function __construct(private readonly PlanoplanRepository $repository)
    {
    }

    public function handle(): void
    {
        $userAgent = 'Multi Kelnik';

        $this->repository
            ->getOld(
                now()->subDays(config('kelnik-estate.planoplan.update.dateEdge')),
                (int)config('kelnik-estate.planoplan.update.limit')
            )
            ->each(function (Planoplan $el) use ($userAgent) {
                if (!$el->version) {
                    LoadPlanoplanData::dispatch(...['planoplan' => $el]);
                    return;
                }

                $response = Http::retry(3, 100, throw: false)
                    ->withUserAgent($userAgent)
                    ->acceptJson()
                    ->get($el->widget::dataUrl($el->getKey()));

                if (!$response->successful() || !$response->json()) {
                    Log::warning(
                        'Error on planoplan widget request',
                        [
                            'error' => $response->clientError(),
                            'job' => self::class
                        ]
                    );
                    return;
                }

                $json = $response->json();

                if (Arr::get($json, 'blocked.isBlocked')) {
                    Log::warning(
                        'Planoplan widget is blocked',
                        [
                            'widgetCode' => $el->getKey(),
                            'job' => self::class
                        ]
                    );
                    return;
                }

                if (md5($el->getRawOriginal('data')) === md5(json_encode($json))) {
                    return;
                }

                $el->data = $json;
                $this->repository->save($el);
            });
    }
}
