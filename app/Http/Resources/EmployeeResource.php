<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        return [
            'id'                 => $this->id,
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date ? $this->end_date : null,
            'ministry'           => [
                'name'           => $this->ministry->translation($locale)->name,
                'abbreviation'   => $this->ministry->abbreviation,
            ],
            'ministry_branch_id' => $this->ministry_branch_id
        ];
    }
}
