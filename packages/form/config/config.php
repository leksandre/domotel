<?php

use Kelnik\Form\Fields\Additional\AdditionalField;
use Kelnik\Form\Fields\Email\EmailField;
use Kelnik\Form\Fields\Phone\PhoneField;
use Kelnik\Form\Fields\Text\TextField;
use Kelnik\Form\Fields\Textarea\TextareaField;

return [
    'fieldTypes' => [
        AdditionalField::class,
        EmailField::class,
        PhoneField::class,
        TextField::class,
        TextareaField::class
    ]
];
