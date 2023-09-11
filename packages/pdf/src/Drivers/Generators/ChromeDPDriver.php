<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Generators;

use ChromeDevtoolsProtocol\Context;
use ChromeDevtoolsProtocol\ContextInterface;
use ChromeDevtoolsProtocol\DevtoolsClientInterface;
use ChromeDevtoolsProtocol\Instance\Instance;
use ChromeDevtoolsProtocol\Instance\InstanceInterface;
use ChromeDevtoolsProtocol\Model\Page\NavigateRequest;
use ChromeDevtoolsProtocol\Model\Page\PrintToPDFRequest;
use ChromeDevtoolsProtocol\Model\Page\SetDocumentContentRequest;
use Kelnik\Pdf\Drivers\Contracts\GeneratorDriver;

/**
 * @see https://chromedevtools.github.io/devtools-protocol/
 * @see https://bugs.chromium.org/p/chromium/issues/detail?id=69227
 */
final class ChromeDPDriver extends GeneratorDriver
{
    private const TIMEOUT = 10;

    private readonly InstanceInterface $instance;

    public function __construct(Config $config)
    {
        parent::__construct($config);

        $url = parse_url($this->config->binPath);
        $this->instance = new Instance($url['host'], $url['port']);
    }

    public function printToBinary(string $url): ?string
    {
        $ctx = Context::withTimeout(Context::background(), self::TIMEOUT);

        $tab = $this->instance->open($ctx);
        $tab->activate($ctx);
        $devTools = $tab->devtools();

        $devTools->page()->enable($ctx);
        $this->loadPage($devTools, $ctx, $url);
        $devTools->page()->awaitLoadEventFired($ctx);

        $pdf = $devTools->page()->printToPDF(
            $ctx,
            PrintToPDFRequest::builder()
                ->setDisplayHeaderFooter($this->config->printHeaderAndFooter)
                ->setPrintBackground($this->config->printBackground)
                ->setMarginTop($this->config->marginTop)
                ->setMarginBottom($this->config->marginBottom)
                ->setMarginLeft($this->config->marginLeft)
                ->setMarginRight($this->config->marginRight)
                ->setPaperWidth($this->config->pageWidth)
                ->setPaperHeight($this->config->pageHeight)
                ->setScale(1)
                ->setLandscape($this->config->pageOrientation === $this->config::ORIENTATION_LANDSCAPE)
                ->build()
        );
        $devTools->close();
        $tab->close($ctx);

        return base64_decode($pdf->data);
    }

    private function loadPage(DevtoolsClientInterface $devTools, ContextInterface $ctx, string $url): void
    {
        if ($this->isUrl($url)) {
            $devTools->page()->navigate($ctx, NavigateRequest::builder()->setUrl($url)->build());
            return;
        }

        $devTools->page()->setDocumentContent(
            $ctx,
            SetDocumentContentRequest::builder()
                ->setFrameId($devTools->page()->getFrameTree($ctx)->frameTree->frame->id)
                ->setHtml($url)
                ->build()
        );
    }
}
