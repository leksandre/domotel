<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\Services\Contracts\PlanoplanService;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Pdf\Services\Contracts\PdfService;
use Orchid\Attachment\Models\Attachment;
use Symfony\Component\HttpFoundation\Response;

final class PremisesCardToPdf implements Contracts\PremisesCardToPdf
{
    use LoadPremises;

    private const CACHE_TTL_DEFAULT = 864_000; // 10 days

    private readonly CoreService $coreService;
    private readonly EstateService $estateService;
    private readonly PlanoplanService $planoplanService;
    private readonly SettingsService $settingsService;
    private readonly PageLinkService $pageLinkService;

    public function __construct(
        private readonly string|int $primary,
        private readonly string $routeName,
        private readonly int|string $pageComponentKey,
        private array $premisesCardData
    ) {
        $this->coreService = resolve(CoreService::class);
        $this->estateService = resolve(EstateService::class);
        $this->settingsService = resolve(SettingsService::class);
        $this->planoplanService = resolve(PlanoplanService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
    }

    public function getLink(): ?string
    {
        if (!$this->serviceIsAvailable()) {
            return null;
        }

        return \route(
            $this->routeName,
            [
                RouteProvider::PARAM_KEY => $this->primary,
                RouteProvider::PARAM_PRINT => RouteProvider::PRINT_TYPE_PDF
            ],
            false
        );
    }

    public function send(): Response
    {
        if (!$this->serviceIsAvailable()) {
            $this->showError();
        }

        $moduleName = EstateServiceProvider::MODULE_NAME;
        $filePath = $this->primary . '.pdf';

        /** @var PdfService $pdfService */
        $pdfService = resolve(PdfService::class);

        $pdf = $pdfService->getFileByPath($moduleName, $filePath);
        $data = Cache::get($this->getCacheId());

        if ($pdf && $data !== null) {
            return $pdf->download($data['element']->typeShortTitle . '.pdf')->send();
        }

        if (!$this->checkLimit()) {
            abort(Response::HTTP_TOO_MANY_REQUESTS);
        }

        $data = $this->getTemplateData();
        $html = view(
            $data['element']->type->typeGroup->living
                ? 'kelnik-estate::components.premisesCard.pdf.residential'
                : 'kelnik-estate::components.premisesCard.pdf.non-residential',
            $data
        )->render();

        try {
            return $pdfService->printToFile($moduleName, $filePath, $html, $this->getCacheTags($data))
                ->download($data['element']->typeShortTitle . '.pdf')
                ->send();
        } catch (Exception $e) {
            Log::error(
                'PDF service error: ' . $e->getMessage(),
                [$e->getCode(), $e->getFile() . ':' . $e->getLine()]
            );
        }

        $this->showError();
    }

    private function getTemplateData(): array
    {
        $res = Cache::get($this->getCacheId());

        if ($res !== null) {
            $res['createDateTime'] = now()->format('d.m.Y, H:i');
            return $res;
        }

        $premises = $this->loadPremisesData();

        if (!$premises) {
            $this->showError();
        }

        $complexSettings = $this->settingsService->getComplex();

        $this->premisesCardData['complex'] = $complexSettings->get('name');
        $this->premisesCardData['logo'] = [
            'light' => $complexSettings->get('logoLight'),
            'dark' => $complexSettings->get('logoDark')
        ];

        if (empty($this->premisesCardData['phone'])) {
            $this->premisesCardData['phone'] = [
                ['value' => $complexSettings->get('phone')]
            ];
        }

        $this->premisesCardData = $this->loadAttach($this->premisesCardData);

        if ($this->coreService->hasModule('contact')) {
            $this->premisesCardData['contacts'] = resolve(ContactService::class)->getOffices()->toArray();
        }

        $hasFloor = $premises->relationLoaded('floor') && $premises->floor->exists;
        $hasBuilding = $hasFloor && $premises->floor->relationLoaded('building') && $premises->floor->building->exists;
        $url = route($this->routeName, [RouteProvider::PARAM_KEY => $this->primary]);
        $host = parse_url($url ?? config('app.url'), PHP_URL_HOST);
        $hostUtf8 = idn_to_utf8($host);

        if ($host !== $hostUtf8) {
            $url = str_replace($host, $hostUtf8, $url);
            $host = $hostUtf8;
        }

        $res = [
            'element' => $premises,
            'hasFloor' => $hasFloor,
            'hasBuilding' => $hasBuilding,
            'hasSection' => $hasFloor && $premises->relationLoaded('section') && $premises->section->exists,
            'hasPop' => mb_strlen($premises->planoplan_code ?? '') > 0,
            'hasPopWidget' => $premises->relationLoaded('planoplan') && $premises->planoplan->widget,
            'hasPlan' => $premises->relationLoaded('imagePlan') && $premises->imagePlan,
            'has3dPlan' =>  $premises->relationLoaded('image3D') && $premises->image3D,
            'hasGallery' => $premises->relationLoaded('gallery') && $premises->gallery->isNotEmpty(),
            'hasFloorPlan' => $premises->relationLoaded('imageOnFloor') && $premises->imageOnFloor,
            'hasBuildingPlan' => $hasBuilding && $premises->floor->building->relationLoaded('complexPlan')
                && $premises->floor->building->complexPlan,
            'hasCompletion' => $hasBuilding && $premises->floor->building->relationLoaded('completion')
                && $premises->floor->building->completion->exists,
            'host' => $host,
            'url' => $url,
            'createDateTime' => now()->format('d.m.Y, H:i'),
            'logo' => $this->premisesCardData['logo'] ?? [],
            'phones' => $this->premisesCardData['phone'] ?? [],
            'schedule' => $this->premisesCardData['schedule'] ?? [],
            'about' => $this->premisesCardData['about'] ?? [],
            'contacts' => $this->premisesCardData['contacts'] ?? []
        ];

        Cache::tags($this->getCacheTags($res))->put($this->getCacheId(), $res, self::CACHE_TTL_DEFAULT);

        return $res;
    }

    private function loadAttach(array $data): array
    {
        if (!$data) {
            return $data;
        }

        $ids = array_merge(
            $data['about']['images'] ?? [],
            array_values($data['logo'] ?? [])
        );

        if (!$ids) {
            return $data;
        }

        /** @var Collection $attaches */
        $attaches = resolve(AttachmentRepository::class)->getByPrimary($ids)?->pluck(null, 'id');

        $replace = static function (array $images, Collection $attaches) {
            foreach ($images as &$el) {
                if (!$el) {
                    continue;
                }

                /** @var ?Attachment $el */
                $el = $attaches[$el] ?? null;

                if ($el) {
                    $el = 'data:' . $el->getMimeType() . ';base64,' .
                        base64_encode(Storage::disk($el->disk)->get($el->physicalPath()));
                }
            }
            unset($el);

            return $images;
        };

        $data['logo'] = $replace($data['logo'] ?? [], $attaches);
        $data['about']['images'] = $replace($data['about']['images'] ?? [], $attaches);

        return $data;
    }

    private function serviceIsAvailable(): bool
    {
        return $this->coreService->hasModule('pdf');
    }

    private function showError(): never
    {
        abort(Response::HTTP_NOT_FOUND);
    }

    private function checkLimit(): bool
    {
        $rateName = 'estate-premises-card-pdf:' . $this->primary;

        if (RateLimiter::tooManyAttempts($rateName, 1)) {
            return false;
        }

        RateLimiter::hit($rateName);

        return true;
    }

    private function getCacheTags(array $data): array
    {
        $res = [
            $this->estateService->getModuleCacheTag(),
            $this->estateService->getPremisesCacheTag($this->primary),
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COMPLEX
            ),
            resolve(PageService::class)->getPageComponentCacheTag($this->pageComponentKey)
        ];

        if (!empty($data['contacts'])) {
            $res[] = resolve(ContactService::class)->getOfficeCacheTag();
        }

        if ($data['hasPopWidget']) {
            $res[] = $this->planoplanService->getCacheTag($data['element']->planoplan->getKey());
        }

        return $res;
    }

    private function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag(
            'pageComponent_' . $this->pageComponentKey . '_pdf_' . md5((string)($this->primary ?? ''))
        );
    }
}
