<?php

namespace App\Http\Controllers;

use App\Http\Requests\MinistryRequest;
use App\Http\Resources\GovernorateResource;
use App\Http\Resources\MinistryResource;
use App\Models\Governorate;
use App\Models\Ministry;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MinistryController extends Controller
{
    use ResponseTrait;

    public function add(MinistryRequest $request)
    {
        $user = Auth::user();
        $ministry = Ministry::create($request->validated());
        if ($ministry)
            return $this->successResponse($ministry, __('messages.ministry_created'), 201);

        return $this->errorResponse(__('messages.registration_failed'), 500);
    }

    public function getMinistries()
    {
        $ministries = Cache::remember('ministries_all', 3600, function () {
            return Ministry::all();
        });
        $data = MinistryResource::collection($ministries);
        return $this->successResponse($data, __('messages.ministries_retrieved'), 200);
    }

    public function getMinistryInfo($ministry_id)
    {
        $cacheKey = 'ministry_info_' . $ministry_id;
        $ministry = Cache::remember($cacheKey, 3600, function () use ($ministry_id) {
            return Ministry::where('id', $ministry_id)->get();
        });

        if ($ministry->isEmpty()) {
            return $this->errorResponse(__('messages.ministry_not_found'), 404);
        }

        $data = MinistryResource::collection($ministry);

        return $this->successResponse($data, __('messages.ministry_branches_retrieved'), 200);
    }

    public function getGovernorates()
    {
        $governorates = Cache::rememberForever('governorates_all', function () {
            return Governorate::all();
        });
        $governorates = GovernorateResource::collection($governorates);
        return $this->successResponse($governorates, __('messages.governorates_retrieved'), 200);
    }
}
