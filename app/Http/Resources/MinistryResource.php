<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinistryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $translation = $this->translation($locale);
        return [
            'id' => $this->id,
            'name' => $translation ? $translation->name : null,
            'abbreviation' => $this->abbreviation,
            'description' => $translation ? $translation->description : null,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d h:i A'),
            'branches' => MinistryBranchResource::collection($this->branches),
        ];
    }
}
