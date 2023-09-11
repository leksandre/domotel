<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;

/**
 * @method File name(string $value = null)
 * @method File required(bool $value = true)
 * @method File size($value = true)
 * @method File src($value = true)
 * @method File value($value = true)
 * @method File help(string $value = null)
 * @method File popover(string $value = null)
 * @method File title(string $value = null)
 * @method File maxFileSize($value = true)
 * @method File storage($value = null)
 * @method File groups($value = true)
 * @method File class($value = null)
 * @method File style($value = null)
 */
class File extends \Orchid\Screen\Fields\Picture
{
    /** @var string */
    protected $view = 'kelnik-core::platform.fields.file';

    /** @var array */
    protected $attributes = [
        'value' => null,
        'target' => 'url',
        'url' => null,
        'origName' => null,
        'maxFileSize' => null,
        'class' => null,
        'style' => null
    ];

    /** @var string[] */
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
        'origname',
        'groups',
        'class',
        'style'
    ];

    public function targetId(): self
    {
        $this->set('target', 'id');

        return $this->addBeforeRender(function () {
            $value = (string) $this->get('value');

            if (! ctype_digit($value)) {
                return;
            }

            /** @var Attachment $attach */
            $attach = Dashboard::model(Attachment::class)::findOrNew($value);

            if (!$attach->exists) {
                return;
            }

            $this->set('url', $attach->url);
            $this->set('origname', $attach->original_name);
        });
    }
}
