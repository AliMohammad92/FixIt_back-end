<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Services\OTPService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    use ResponseTrait;

    public function add(AddEmployeeRequest $request, OTPService $otpService)
    {
        $data = $request->validated();
        $dataUser = $request->only(['first_name', 'last_name', 'email', 'phone', 'role', 'address']);
        $dataUser['password'] = bcrypt($dataUser['first_name'] . '12345');
        $user = User::create($dataUser);
        $employee = $user->employee()->create([
            'position' => $data['position'],
            'start_date' => $data['start_date'],
            'ministry_branch_id' => $data['branch_id'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);

        if (!$user || !$employee) {
            $user ? $user->delete() : null;
            return $this->errorResponse(
                __('messages.employee_creation_failed'),
                [],
                500
            );
        }

        $user->assignRole('employee');
        $otpService->resendOTP($user->id);

        return $this->successResponse(
            __('messages.employee_added'),
            ['employee' => $user->employee],
            201
        );
    }

    public function getEmployees()
    {
        $employees = EmployeeResource::collection(Employee::all());
        return $this->successResponse(
            __('messages.employees_retrieved'),
            ['employees' => $employees],
            200
        );
    }

    public function getEmployeesInBranch($ministry_branch_id)
    {
        $employees = EmployeeResource::collection(Employee::where('ministry_branch_id', $ministry_branch_id)->get());
        return $this->successResponse(
            __('messages.employees_retrieved'),
            ['employees' => $employees],
            200
        );
    }

    public function promoteEmployee(Request $request, $employee_id)
    {
        $request->validate([
            'new_position' => 'required|string|max:255',
            'new_end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $employee = Employee::findOrFail($employee_id);
        $postsion = $request->input('new_position');
        $user = Auth::user();
        $promotionRules = [
            'ministry_manager' => [
                'allowed_roles' => ['super_admin'],
                'sync_roles'    => ['employee', 'ministry_manager'],
            ],
            'branch_manager' => [
                'allowed_roles' => ['super_admin', 'ministry_manager'],
                'sync_roles'    => ['employee', 'branch_manager'],
            ],
        ];

        if (!isset($promotionRules[$postsion])) {
            return $this->errorResponse(
                __('messages.unauthorized_promotion'),
                [],
                403
            );
        }
        $allowedRoles = $promotionRules[$postsion]['allowed_roles'];
        if (!$user->hasAnyRole($allowedRoles)) {
            return $this->errorResponse(
                __('messages.unauthorized_promotion'),
                [],
                403
            );
        }

        if ($request->filled('new_end_date')) {
            $employee->end_date = $request->input('new_end_date');
        }
        $employee->position = $postsion;
        $employee->user->syncRoles($promotionRules[$postsion]['sync_roles']);

        $employee->save();

        return $this->successResponse(
            __('messages.employee_promoted'),
            ['employee' => $employee],
            200
        );
    }
}
