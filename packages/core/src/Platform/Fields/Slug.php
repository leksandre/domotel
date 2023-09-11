<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Orchid\Screen\Fields\Input;

/**
 * @method Slug additionalFields(array $arr = [])
 * @method Slug source(string $value = null)
 * @method Slug sourceId(string $value = null)
 * @method Slug method(string $value = null)
 */
class Slug extends Input
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.slug';

    /** @var array<string, mixed> */
    protected $attributes = [
        'class' => 'form-control',
        'source' => null,
        'sourceId' => 0,
        'additionalFields' => '',
        'method' => null,
        'actionUrl' => null
    ];

    public function __construct()
    {
        parent::__construct();

        $this->addBeforeRender(function () {
            $url = url()->current();
            $this->set(
                'actionUrl',
                rtrim("$url/{$this->get('method')}", '/?')
            );
        });
    }
}
