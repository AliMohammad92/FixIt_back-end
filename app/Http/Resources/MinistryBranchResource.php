<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MinistryBranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $translation = $this->translation($locale);
        return [
            'id' => $this->id,
            'name' => $translation ? $translation->name : null,
            'governorate_id' => $this->governorate_id,
            'governorate_name' => DB::table('governorates')->where('id', $this->governorate_id)->value('name'),
            'governorate_code' => DB::table('governorates')->where('id', $this->governorate_id)->value('code'),
            'ministry_id' => $this->ministry_id,
            'created_at' => $this->created_at->format('Y-m-d H:i A'),
        ];
    }
}
