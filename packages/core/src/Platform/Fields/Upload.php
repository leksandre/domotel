<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Illuminate\Support\Arr;
use Orchid\Attachment\Models\Attachment;
use Orchid\Platform\Dashboard;
use Orchid\Support\Assert;
use Orchid\Support\Init;

/**
 * @method Upload chunking(bool $value)
 * @method Upload chunkSize(int $value)
 */
class Upload extends \Orchid\Screen\Fields\Upload
{
    protected $view = 'kelnik-core::platform.fields.upload';

    protected $attributes = [
        'value'           => null,
        'multiple'        => false,
        'parallelUploads' => 0,
        'maxFileSize'     => null,
        'maxFiles'        => 9999,
        'timeOut'         => 0,
        'acceptedFiles'   => null,
        'resizeQuality'   => 0.9,
        'resizeWidth'     => null,
        'resizeHeight'    => null,
        'media'           => false,
        'closeOnAdd'      => false,
        'visibility'      => 'public',
        'chunking'        => false,
        'chunkSize'       => 0
    ];

    public function __construct()
    {
        // Set max file size
        $this->addBeforeRender(function () {
            $maxFileSize = $this->get('maxFileSize');

            $serverMaxFileSize = Init::maxFileUpload(Init::MB);

            if ($maxFileSize === null) {
                $this->set('maxFileSize', $serverMaxFileSize);

                return;
            }

            throw_if(
                $maxFileSize > $serverMaxFileSize,
                'Cannot set the desired maximum file size. This contradicts the settings specified in .ini'
            );
        });

        // set load relation attachment
        $this->addBeforeRender(function () {
            $value = Arr::wrap($this->get('value'));

            if (! Assert::isIntArray($value)) {
                return;
            }

            /** @var Attachment $attach */
            $attach = Dashboard::model(Attachment::class);

            $value = $value
                ? $attach::whereIn('id', $value)
                    ->orderByRaw('FIELD(`id`, ' . implode(',', $value) . ')')
                    ->get()
                    ->toArray()
                : [];

            $this->set('value', $value);
        });

        // Division into groups
        $this->addBeforeRender(function () {
            $group = $this->get('groups');

            if ($group === null) {
                return;
            }

            $value = collect($this->get('value', []))
                ->where('group', $group)
                ->toArray();

            $this->set('value', $value);
        });
    }
}
