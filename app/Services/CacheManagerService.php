<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheManagerService
{
    public function clearComplaintCache(
        int $citizenId = null,
        int $branchId = null,
        int $ministryId = null,
        int $single = null,
    ) {
        Cache::forget("complaints");
        if ($citizenId) {
            Cache::forget("complaints:citizen:{$citizenId}");
        }

        if ($branchId) {
            Cache::forget("complaints:branch:{$branchId}");
        }

        if ($ministryId) {
            Cache::forget("complaints:ministry:{$ministryId}");
        }

        if ($single) {
            Cache::forget("complaints:single:{$single}");
            Cache::forget("complaint:{$single}:replies");
        }
    }
}
