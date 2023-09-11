<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\FirstScreen\Layouts;

use Closure;
use Illuminate\Support\Arr;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\MatrixStatic;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Estate\Platform\Services\Contracts\EstatePlatformService;
use Kelnik\News\Models\Element;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\View\Components\FirstScreen\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    private ?NewsPlatformService $newsPlatformService;
    private ?EstatePlatformService $estatePlatformService;

    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
        $this->newsPlatformService = resolve(NewsPlatformService::class);
        $this->estatePlatformService = DataProvider::moduleEstateExists()
            ? resolve(EstatePlatformService::class)
            : null;
    }

    protected function fields(): array
    {
        $res = [
            Input::make('data.content.slogan')
                ->title('kelnik-page::admin.components.firstScreen.slogan')
                ->placeholder('kelnik-page::admin.components.firstScreen.sloganPlaceholder')
                ->maxlength(255),
            Input::make('data.content.complexName')
                ->title('kelnik-page::admin.components.firstScreen.complexName')
                ->placeholder('kelnik-page::admin.components.firstScreen.complexNamePlaceholder')
                ->maxlength(255)
                ->hr(),
            Input::make('data.content.video')
                ->type('url')
                ->title('kelnik-page::admin.components.firstScreen.video')
                ->placeholder('kelnik-page::admin.components.firstScreen.videoPlaceholder')
                ->help('kelnik-page::admin.components.firstScreen.videoHelp')
                ->maxlength(255),
            Upload::make('data.content.slider')
                ->title('kelnik-page::admin.components.firstScreen.slider')
                ->acceptedFiles('image/*')
                ->groups(PageServiceProvider::MODULE_NAME)
                ->hr(),
            Matrix::make('data.content.advantages')
                ->title('kelnik-page::admin.components.firstScreen.advantageHeader')
                ->sortable(true)
                ->columns([
                    trans('kelnik-page::admin.components.firstScreen.advantageTitle') => 'title'
                ])
                ->fields(['title' => Input::make()])
                ->hr()
        ];

        if (DataProvider::moduleNewsExists()) {
            $res[] = Title::make('')->value(trans('kelnik-page::admin.components.firstScreen.action.title'));
            $res[] = Relation::make('data.content.action.id')
                        ->title('kelnik-page::admin.components.firstScreen.action.title')
                        ->fromModel(Element::class, 'title')
                        ->applyScope('RelationList')
                        ->searchColumns('id', 'title')
                        ->displayAppend('relation')
                        ->allowEmpty();

            $res[] = Input::make('data.content.action.button.text')
                        ->title('kelnik-page::admin.components.firstScreen.action.button.text')
                        ->help('kelnik-page::admin.components.firstScreen.action.button.limit')
                        ->maxlength(DataProvider::ACTION_BUTTON_TEXT_LIMIT);
            $res[] = Input::make('data.content.action.buttonLink')
                        ->title('kelnik-page::admin.components.firstScreen.action.button.link')
                        ->help('kelnik-page::admin.components.firstScreen.action.button.limit')
                        ->maxlength(DataProvider::ACTION_BUTTON_LINK_LIMIT);

            $res[] = Picture::make('data.content.action.icon')
                        ->title('kelnik-page::admin.components.firstScreen.action.icon.title')
                        ->help('kelnik-page::admin.components.firstScreen.action.icon.help')
                        ->groups(PageServiceProvider::MODULE_NAME)
                        ->targetId();

            $res[] = $this->newsPlatformService->getContentLink();
        }

        if (DataProvider::moduleEstateExists()) {
            $res[] = Title::make('')->value(trans('kelnik-page::admin.components.firstScreen.estate.title'));

            $variants = $this->estatePlatformService->getElements();
            $currentVariants = Arr::get($this->query->get('data'), 'content.estate.types', []);

            if ($currentVariants && $variants) {
                foreach ($currentVariants as $curIndex => $curType) {
                    foreach ($variants as &$variant) {
                        if (!empty($curType['id']) && $variant['id'] === (int)$curType['id']) {
                            $variant['active'] = $curType['active'] ?? 0;
                            $variant['title'] = $curType['title'] ?? $variant['title'] ?? '';
                            $variant['url'] = $curType['url'] ?? '';
                            $variant['sort'] = $curIndex;
                            continue 2;
                        }
                    }
                }

                usort($variants, static fn($curEl, $nextEl) => ($curEl['sort'] ?? 0) <=> ($nextEl['sort'] ?? 0));
            }

            $res[] = MatrixStatic::make('data.content.estate.types')
                ->sortable(true)
                ->columns([
                    'ID' => 'id',
                    trans('kelnik-page::admin.components.firstScreen.estate.types.active') => 'active',
                    trans('kelnik-page::admin.components.firstScreen.estate.types.title') => 'title',
                    trans('kelnik-page::admin.components.firstScreen.estate.types.url') => 'url',
                ])
                ->fields([
                    'id' => Input::make()
                        ->readonly()
                        ->style('width:30px; background:none; margin:0; padding: .2rem'),
                    'active' => Switcher::make()->sendTrueOrFalse(),
                    'title' => Input::make()->maxlength(100),
                    'url' => Input::make()->type('text')->maxlength(255)
                ])
                ->addBeforeRender(function () use ($variants) {
                    $this->set('value', $variants);
                });

            $res[] = $this->estatePlatformService->getContentLink();
        }

        $res[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $res;
    }
}
