<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MinistryBranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_name' => $this->name,
            'governorate_id' => $this->governorate_id,
            'governorate_name' => DB::table('governorates')->where('id', $this->governorate_id)->value('name'),
            'governorate_code' => DB::table('governorates')->where('id', $this->governorate_id)->value('code'),
            'ministry_id' => $this->ministry_id,
            'ministry_name' => $this->ministry->ministry_name,
            'created_at' => $this->created_at->format('Y-m-d H:i A'),
        ];
    }
}
