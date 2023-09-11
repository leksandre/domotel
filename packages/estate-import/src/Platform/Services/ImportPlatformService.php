<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Services;

use Cron\CronExpression;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Jobs\AddDataFromSource;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Kelnik\EstateImport\Sources\Csv\CsvUploadedFile;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

final class ImportPlatformService implements Contracts\ImportPlatformService
{
    public function __construct(
        private readonly HistoryRepository $repository,
        private readonly CoreService $coreService,
        private readonly SettingsRepository $settingsRepository
    ) {
    }

    public function addImportFile(Request $request): RedirectResponse
    {
        $fileField = 'file';
        $imagesField = 'images';

        $request->validate([
            $fileField => 'required|array',
            $imagesField => 'nullable|array'
        ]);

        $importFile = (int)current($request->post($fileField, []));
        $importImagesArch = (int)current($request->post($imagesField, []));

        if (!$importFile) {
            redirect()->back();
        }

        $history = new History();
        $history->save();
        $preProcessor = new CsvUploadedFile($history);

        if (!$preProcessor->prepareData($importFile, $importImagesArch)) {
            redirect()->back();
        }

        Toast::info(trans('kelnik-estate-import::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estateImport.history'));
    }

    public function downloadLogFile(string $logName): BinaryFileResponse
    {
        $logPath = config('kelnik-estate-import.logging.config.path');

        abort_if(!$logPath, Response::HTTP_NOT_FOUND);

        $filePathInfo = pathinfo($logPath);
        $filePath = str_replace($filePathInfo['basename'], $filePathInfo['filename'] . '-' . $logName, $logPath);

        abort_if(!file_exists($filePath), Response::HTTP_NOT_FOUND);

        return \response()->download($filePath, $logName);
    }

    public function getLogLink(DateTimeInterface $dateTime): ?string
    {
        return route(
            $this->coreService->getFullRouteName('estateImport.getLog'),
            ['logName' => $dateTime->format('Y-m-d') . '.log']
        );
    }

    public function saveSourceList(Request $request): RedirectResponse
    {
        $module = EstateImportServiceProvider::MODULE_NAME;
        $name = 'source';

        $request->validate([
            $name . '.list' => 'nullable|array',
            $name . '.list.*.url' => 'url'
        ]);

        $setting = $this->settingsRepository->get($module, $name) ?? new Setting();

        if (!$setting->exists) {
            $setting->module = $module;
            $setting->name = $name;
        }

        $setting->value = array_values($request->input($name . '.list'));

        $this->settingsRepository->set($setting);

        Toast::info(trans('kelnik-estate-import::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estateImport.history'));
    }

    public function saveReplacementList(Request $request): RedirectResponse
    {
        $module = EstateImportServiceProvider::MODULE_NAME;
        $name = 'replacement';

        $request->validate([
            $name . '.list' => 'nullable|array',
            $name . '.list.*.src' => 'required',
            $name . '.list.*.dst' => 'required'
        ]);

        $setting = $this->settingsRepository->get($module, $name) ?? new Setting();

        if (!$setting->exists) {
            $setting->module = $module;
            $setting->name = $name;
        }

        $setting->value = array_values($request->input($name . '.list'));

        $this->settingsRepository->set($setting);

        Toast::info(trans('kelnik-estate-import::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estateImport.history'));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        /** @var ImportSettingsService $importSettingsService */
        $importSettingsService = resolve(ImportSettingsService::class);

        /** @var SourceType $source */
        $source = $importSettingsService->getSourceByName($request->input('settings.source'));

        $importSettingsService->saveSourceParams($source, $request->input('settings.' . $source->getName(), []));
        $importSettingsService->saveSource($source);

        Toast::info(trans('kelnik-estate-import::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estateImport.settings'));
    }

    public function getScheduleNextDueDate(): string
    {
        /** @var Schedule $scheduler */
        $scheduler = resolve(Schedule::class);
        $taskCron = current(
            array_filter(
                config('kelnik-estate-import.schedule'),
                fn($val, $key) => $key === AddDataFromSource::class,
                ARRAY_FILTER_USE_BOTH
            )
        );

        if (!$taskCron) {
            return '-';
        }

        $event = $scheduler->job(AddDataFromSource::class)->cron($taskCron);
        $timezone = new DateTimeZone(config('app.timezone'));

        return Carbon::instance(
            (new CronExpression($event->expression))
                ->getNextRunDate(Carbon::now()->setTimezone($event->timezone))
                ->setTimezone($timezone)
        )->diffForHumans(parts: 2);
    }
}
