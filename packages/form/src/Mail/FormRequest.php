<?php

declare(strict_types=1);

namespace Kelnik\Form\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Form\Models\Log;

class FormRequest extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Log $formLog)
    {
    }

    public function build(): self
    {
        $this->formLog->load('form');
        $complex = resolve(SettingsService::class)->getComplex();
        $subject = $this->formLog->form->notify_title ?: $this->formLog->form->title;

        return $this
            ->from(
                $complex->get('emailReply') ?? config('mail.from.address'),
                $complex->get('name') ?? config('mail.from.name')
            )
            ->subject($subject)
            ->view(
                'kelnik-form::mail.request',
                [
                    'header' => $subject,
                    'link' => $this->formLog->data['sourceUrl'] ?? '',
                    'fields' => $this->formLog->data['fields'] ?? [],
                    'date' => $this->formLog->created_at->translatedFormat('d F Y, H:i')
                ]
            );
    }
}
