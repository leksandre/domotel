<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Email;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Repositories\Contracts\FormRepository;

final class FormEloquentRepository implements FormRepository
{
    /** @var class-string $model */
    private string $model = Form::class;

    public function isUnique(Form $form): bool
    {
        $query = $this->model::query()->where('slug', '=', $form->slug)->limit(1);

        if ($form->exists) {
            $query->whereKeyNot($form->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function findByPrimary(int|string $primary): Form
    {
        return $this->model::findOrNew($primary);
    }

    public function findActiveByPrimary(int|string $primary): Form
    {
        return $this->model::where('active', true)->findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('title')->get();
    }

    public function getAdminListPaginated(): LengthAwarePaginator
    {
        return $this->model::filters()->defaultSort('id')->withCount(['fields', 'emails', 'logs'])->paginate();
    }

    /**
     * @param Form $model
     * @param string[]|null $emails
     *
     * @return bool
     */
    public function save(Model $model, ?array $emails = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$emails) {
            if (is_array($emails)) {
                $model->emails()->delete();
            }

            return $res;
        }

        $emails = new Collection(array_values($emails));
        $model->emails->each(static function (Email $email) use (&$emails) {
            $newIndex = $emails->search($email->email);

            if ($newIndex === false) {
                $email->delete();

                return;
            }

            $emails->forget($newIndex);
        });

        if ($emails->isEmpty()) {
            return $res;
        }

        $emails->each(static function (string $emailAddress) use ($model) {
            $email = new Email(['email' => $emailAddress]);
            $email->form()->associate($model);
            $email->save();
        });

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
