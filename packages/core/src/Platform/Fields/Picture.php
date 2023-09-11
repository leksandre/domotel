<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

class Picture extends \Orchid\Screen\Fields\Picture
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.picture';

    /* @var array */
    protected $attributes = [
        'value' => null,
        'target' => 'url',
        'url' => null,
        'maxFileSize' => null,
        'acceptedFiles' => 'image/*',
        'class' => null,
        'style' => null
    ];

    /** @var array */
    protected $inlineAttributes = [
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'name',
        'placeholder',
        'readonly',
        'required',
        'tabindex',
        'value',
        'target',
        'url',
        'groups',
        'path',
        'class',
        'style'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->addBeforeRender(function () {
            if ($this->get('value') === 0) {
                $this->set('value', null);
            }
        });
    }
}
