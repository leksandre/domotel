<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Screen\Field;

interface NewsPlatformService
{
    public function __construct(
        CategoryRepository $categoryRepo,
        ElementRepository $elementRepo,
        CoreService $coreService,
        PageLinkService $pageLinkService
    );

    public function saveCategory(Category $category, Request $request): RedirectResponse;

    public function createLinkToPage(Category $category, array $sitePages): void;

    public function saveElement(Element $element, Request $request): RedirectResponse;

    public function getContentLink(): Field;

    public function createSlugByTitle(string $title): string;
}
