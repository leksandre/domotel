<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Screen\Layout;

/**
 * Get component data from DB
 * Save component data to DB
 * Store layouts list for Orchid Services
 * Handling custom methods for Orchid Services
 */
abstract class ComponentDataProvider implements DataProvider, Arrayable
{
    protected string|KelnikPageComponent $componentNamespace;

    /** Assigned component data from PageComponent */
    protected Collection $data;

    public function __construct(string $viewComponentNameSpace, int $pageId = 0)
    {
        if (!is_a($viewComponentNameSpace, KelnikPageComponent::class, true)) {
            throw new InvalidArgumentException('Instance of `KelnikViewComponent` required');
        }

        $this->componentNamespace = $viewComponentNameSpace;
        $this->data = new Collection();
    }

    /** Modify content of screen repository */
    public function modifyQuery(array $data): array
    {
        return $data;
    }

    public function getComponentNamespace(): string
    {
        return $this->componentNamespace;
    }

    public function getComponentCode(): string
    {
        return $this->componentNamespace::getCode();
    }

    /** The title of the page component. By default, returns the original name of component */
    public function getComponentTitle(): string
    {
        return static::getComponentTitleOriginal();
    }

    /** Original component title */
    public function getComponentTitleOriginal(): string
    {
        return $this->componentNamespace::getTitle();
    }

    public function getModuleName(): string
    {
        return $this->componentNamespace::getModuleName();
    }

    public function getCommandBar(array $commandBar): array
    {
        return $commandBar;
    }

    /**
     * Returns Orchid platform edit layouts
     *
     * @return Layout[]
     */
    abstract public function getEditLayouts(): array;

    /** Returns color names settings */
    protected function getThemeFields(): Collection
    {
        return collect();
    }

    /** Assigns data in the admin section */
    abstract public function setDataFromRequest(PageComponent $pageComponent, Request $request): void;

    /** Assign default data */
    abstract public function setDefaultValue(): void;

    /** Successful save handler */
    public function afterSaveHandler(): void
    {
        resolve(AttachmentRepository::class)->deleteMass($this->getDeleteAttachIds());
    }

    /** Executed on component deleting */
    public function delete(): void
    {
        resolve(AttachmentRepository::class)->deleteMass($this->getAttachIds($this->data));
    }

    protected function getDeleteAttachIds(): array
    {
        return [];
    }

    protected function getAttachIds(Collection $originData): array
    {
        return [];
    }

    public function getValue(): Collection
    {
        return $this->data;
    }

    public function setValue(Collection $data): void
    {
        $this->data = $data;
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function put(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->data->toArray();
    }
}
