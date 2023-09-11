<?php

declare(strict_types=1);

namespace Kelnik\Progress\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ProgressAlbumVideo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !preg_match(
                '!^(https?://)?(www\.)?(player\.vimeo\.com/video/\d+|youtube\.com/embed/[a-z0-9]+).*$!i',
                $value
            )
        ) {
            $fail(trans('kelnik-progress::validation.albumVideo'));
        }
    }
}
