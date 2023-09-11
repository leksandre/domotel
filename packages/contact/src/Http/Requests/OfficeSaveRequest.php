<?php

declare(strict_types=1);

namespace Kelnik\Contact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Kelnik\Contact\Dto\OfficeDto;
use Kelnik\Core\Map\Contracts\Coords;

final class OfficeSaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'office.title' => 'required|max:255',
            'office.active' => 'boolean',
            'office.phone' => 'nullable|max:100|regex:/^\+?[0-9()\- ]+$/i',
            'office.email' => 'nullable|max:100|email',
            'office.region' => 'nullable|max:255',
            'office.city' => 'nullable|max:255',
            'office.street' => 'nullable|max:255',
            'office.route_link' => 'nullable|max:255',
            'office.image_id' => 'nullable|integer',
            'office.coords' => 'nullable|array',
            'office.coords.lat' => 'nullable|numeric',
            'office.coords.lng' => 'nullable|numeric',
            'office.schedule' => 'nullable|array',
            'office.schedule.*.day' => 'max:255',
            'office.schedule.*.time' => 'max:255',
        ];
    }

    public function getDto(): OfficeDto
    {
        $data = Arr::get($this->safe()->only('office'), 'office');
        $data['active'] = (bool)$data['active'];
        $data['schedule'] = array_values($data['schedule'] ?? []);

        if (!empty($data['image_id'])) {
            $data['image_id'] = (int)$data['image_id'];
        }

        $coords = array_values($data['coords'] ?? [Coords::DEFAULT_COORDS, Coords::DEFAULT_COORDS]);
        $data['coords'] = resolve(Coords::class, [
            'lat' => (float)$coords[0],
            'lng' => (float)$coords[1]
        ]);

        return new OfficeDto(...$data);
    }
}
