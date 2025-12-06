<?php

namespace App\Http\Controllers;

use App\Http\Resources\CitizenResource;
use App\Models\User;
use App\Services\CitizenService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenController extends Controller
{
    use ResponseTrait;
    protected CitizenService $service;

    public function __construct(CitizenService $citizenService)
    {
        $this->service = $citizenService;
    }

    public function read()
    {
        $citizens = $this->service->read();
        if ($citizens->isEmpty()) {
            return $this->successResponse([], __('messages.empty'));
        }

        return $this->successResponse(CitizenResource::collection($citizens), __('messages.citizens_retrieved'));
    }

    public function readOne($id)
    {
        $citizen = $this->service->readOne($id);
        if (!$citizen) {
            return $this->errorResponse(__('messages.user_not_found'), 404);
        }
        return $this->successResponse(new CitizenResource($citizen), __('messages.citizen_retrieved'));
    }

    public function myAccount()
    {
        $citizen = $this->service->readOne(Auth::user()->citizen->id);
        if (!$citizen) {
            return $this->errorResponse(__('messages.user_not_found'), 404);
        }
        return $this->successResponse(new CitizenResource($citizen), __('messages.citizen_retrieved'));
    }
}
