<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Services;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Models\Program;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;

final class MortgageService implements Contracts\MortgageService
{
    public function __construct(
        private BankRepository $bankRepository,
        private CoreService $coreService
    ) {
    }

    public function getBanksListWithSummary(array $banksIds = [], bool $programParamsRange = false): Collection
    {
        $res = $this->bankRepository->getActiveWithPrograms($banksIds);

        if ($res->isEmpty()) {
            return $res;
        }

        $fields = [
            'min' => [
                'minPaymentPercent' => 'min_payment_percent',
                'minTime' => 'min_time',
                'minRate' => 'rate'
            ],
            'max' => [
                'maxPaymentPercent' => 'max_payment_percent',
                'maxTime' => 'max_time',
                'maxRate' => 'rate'
            ]
        ];

        $hasImageModule = $this->coreService->hasModule('image');

        $res->each(static function (Bank $bank) use ($fields, $hasImageModule) {
            $bank->programsParamRange = new Collection([
                'minPaymentPercent' => false,
                'maxPaymentPercent' => 0,
                'minTime' => false,
                'maxTime' => 0,
                'minRate' => false,
                'maxRate' => 0
            ]);

            if (
                $bank->logo->exists
                && strtolower($bank->logo->extension) !== 'svg'
                && $hasImageModule
            ) {
                $imageFile = new ImageFile($bank->logo);
                $params = new Params($imageFile);
                $params->width = Bank::LOGO_WIDTH;
                $params->height = Bank::LOGO_HEIGHT;
                $params->crop = true;
                $bank->logoResizedPath = Picture::getResizedPath($imageFile, $params);
            }

            if (!$bank->relationLoaded('programs') || $bank->programs->isEmpty()) {
                return;
            }

            $bank->programs->each(static function (Program $program) use ($bank, $fields) {
                foreach ($fields as $method => $params) {
                    foreach ($params as $k => $v) {
                        if ($method === 'min' && $bank->programsParamRange[$k] === false) {
                            $bank->programsParamRange[$k] = $program->{$v};
                            continue;
                        }
                        $bank->programsParamRange[$k] = $method($bank->programsParamRange[$k], $program->{$v});
                    }
                }
            });
        });

        return $res;
    }

    public function getBankCacheTag(int|string $id): ?string
    {
        return $id ? 'mortgageBank_' . $id : null;
    }

    public function getBankListCacheTag(): string
    {
        return 'mortgageBankList';
    }
}
