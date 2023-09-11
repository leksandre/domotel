<?php

declare(strict_types=1);

namespace Kelnik\Form\Services;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Kelnik\Form\Events\LogAddedEvent;
use Kelnik\Form\Fields\Contracts\FieldType;
use Kelnik\Form\Fields\FormField;
use Kelnik\Form\Models\Field;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Models\Log;
use Kelnik\Form\Repositories\Contracts\FormLogRepository;
use Kelnik\Form\Repositories\Contracts\FormRepository;

final class FormService implements Contracts\FormService
{
    private Form $form;
    private array $errors = [];
    private array $result = [];

    public function __construct(int|string $primary)
    {
        $this->form = resolve(FormRepository::class)->findByPrimary($primary);

        if (!$this->form->exists || !$this->form->active) {
            throw new InvalidArgumentException('Form not found');
        }
    }

    public function build(): array
    {
        if (!$this->form->exists) {
            return [];
        }

        $res = $this->form->attributesToArray();
        unset(
            $res['created_at'],
            $res['updated_at'],
            $res['active']
        );

        $res['fields'] = [];
        $res['spamFieldName'] = $this->getSpamFieldName();

        $form = &$this->form;
        $this->form->fields->each(static function (Field $field) use ($form, &$res) {
            $fieldObj = $field->type;
            if (!$field->active || !class_exists($fieldObj) || !is_a($fieldObj, FieldType::class, true)) {
                return;
            }

            $fieldObj = new $fieldObj(
                $form->slug,
                FormField::FIELD_PREFIX . $field->getKey(),
                $field->title,
                $field->params
            );

            $res['fields'][] = $fieldObj->render();
        });

        $res['fields'] = implode($res['fields']);

        return $res;
    }

    public function submit(Request $request): bool
    {
        if (!$this->form->exists || !$this->form->active) {
            $this->errors[] = 'Form not found';

            return false;
        }

        if ($request->input($this->getSpamFieldName())) {
            $this->errors[] = 'Spam detected';

            return false;
        }

        $errors = &$this->errors;
        $result = &$this->result;
        $form = &$this->form;
        $data = $request->input($form->slug);
        $files = $request->files;

        $fields = $this->form->fields->filter(
            static fn(Field $field) =>
                $field->active && class_exists($field->type) && is_a($field->type, FieldType::class, true)
        );

        // Validate
        //
        $fields->each(static function (Field $field) use (&$errors, &$result, $form, $data, $files) {
            $fieldObj = $field->type;

            /** @var FormField $fieldObj */
            $fieldObj = new $fieldObj(
                $form->slug,
                FormField::FIELD_PREFIX . $field->getKey(),
                $field->title,
                $field->params
            );

            $validate = $fieldObj->validate($data, $files);

            if ($validate !== true) {
                $errors = array_merge($errors, $validate);
            }
        });

        if ($this->errors) {
            return false;
        }

        // Process
        //
        $fields->each(static function (Field $field) use (&$errors, &$result, $form, $data, $files) {
            $fieldObj = $field->type;
            /** @var FormField $fieldObj */
            $fieldObj = new $fieldObj(
                $form->slug,
                FormField::FIELD_PREFIX . $field->getKey(),
                $field->title,
                $field->params
            );

            $result[] = [
                'title' => $field->title,
                'name' => $fieldObj->getName(),
                'value' => $fieldObj->process($data, $files)
            ];
        });

        $data = [
            'fields' => $this->result,
            'sourceUrl' => $request->headers->get('referer'),
            'client' => [
                'ip' => $request->getClientIps(),
                'browser' =>  $request->headers->get('user_agent')
            ]
        ];

        $resultObj = new Log(['data' => $data]);
        $resultObj->form()->associate($this->form);
        resolve(FormLogRepository::class)->save($resultObj);
        LogAddedEvent::dispatch($resultObj);

        return true;
    }

    public function getLastErrors(): array
    {
        return $this->errors;
    }

    private function getSpamFieldName(): string
    {
        return $this->form->slug . '[' . md5($this->form->slug) . ']';
    }
}
