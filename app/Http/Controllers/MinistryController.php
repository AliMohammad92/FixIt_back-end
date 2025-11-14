<?php

namespace App\Http\Controllers;

use App\Http\Requests\MinistryRequest;
use App\Models\Ministry;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class MinistryController extends Controller
{
    use ResponseTrait;

    public function add(MinistryRequest $request)
    {
        $ministry = Ministry::create($request->validated());
        if ($ministry)
            return $this->successResponse($ministry, __('messages.ministry_created'), 201);

        return $this->errorResponse(__('messages.registration_failed'), 500);
    }
}
