<?php

declare(strict_types=1);

namespace Kelnik\Page\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class VideoLink implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !preg_match(
                '!^(https?://)?(www\.)?(vimeo\.com/\d+|youtu\.be/[a-z0-9]+).*$!i',
                $value
            )
        ) {
            $fail(trans('kelnik-page::validation.video'));
        }
    }
}
