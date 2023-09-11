<?php

declare(strict_types=1);

namespace Kelnik\Estate\Jobs;

use Faker\Factory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

final class LoadPlanoplanData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 1800; // 30min

    public function __construct(private Planoplan $planoplan)
    {
    }

    public function handle(): void
    {
        if (!$this->planoplan->data || !$this->planoplan->version) {
            $this->fillData();
        }

        resolve(PlanoplanRepository::class)->save($this->planoplan);
    }

    private function fillData(): void
    {
        $versions = array_reverse(
            config('kelnik-estate.planoplan.widget.classes', []),
            true
        );
        $userAgent = Factory::create()->userAgent();

        /** @var Planoplan\Contracts\Widget $className */
        foreach ($versions as $version => $className) {
            $response = Http::retry(3, 100, throw: false)
                ->withUserAgent($userAgent)
                ->acceptJson()
                ->get($className::dataUrl($this->planoplan->getKey()));

            if (!$response->successful() || !$response->json()) {
                continue;
            }

            $this->planoplan->data = $response->json();
            $this->planoplan->version = $version;
            $this->planoplan->active = true;

            return;
        }
    }

    public function uniqueId(): string
    {
        return $this->planoplan->getKey();
    }
}
