<?php

namespace App\Http\Controllers;

use App\DAO\EmployeeDAO;
use App\Http\Requests\AddEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use App\Services\OTPService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EmployeeController extends Controller
{
    use ResponseTrait;

    protected $dao;

    public function __construct()
    {
        $this->dao = new EmployeeDAO();
    }

    public function add(AddEmployeeRequest $request, EmployeeService $employeeService, OTPService $otpService)
    {
        $data = $request->validated();
        $dataUser = $request->only(['first_name', 'last_name', 'email', 'phone', 'role', 'address']);
        $dataUser['password'] = bcrypt($dataUser['first_name'] . '12345');

        $user = $this->dao->add($data, $dataUser);

        if (!$user || !$user->employee) {
            $user ? $user->delete() : null;
            return $this->errorResponse(
                [],
                __('messages.employee_creation_failed'),
                500
            );
        }

        $employeeService->add($user, $otpService);
        return $this->successResponse(
            ['employee' => $user->employee],
            __('messages.employee_added'),
            201
        );
    }

    public function getEmployees()
    {
        $cacheKey = 'employees_all';
        $employees = Cache::remember($cacheKey, 3600, function () {
            return $this->dao->getAll();
        });
        $employees = EmployeeResource::collection($employees);
        return $this->successResponse(
            ['employees' => $employees],
            __('messages.employees_retrieved'),
            200
        );
    }

    public function getEmployeesInBranch($ministry_branch_id)
    {
        $cacheKey = 'employees_branch_' . $ministry_branch_id;
        $employees = Cache::remember($cacheKey, 3600, function () use ($ministry_branch_id) {
            return $this->dao->getEmployeesInBranch($ministry_branch_id);
        });

        $employees = EmployeeResource::collection($employees);
        return $this->successResponse(
            ['employees' => $employees],
            __('messages.employees_retrieved'),
            200
        );
    }

    public function promoteEmployee(Request $request, $employee_id, EmployeeService $employeeService)
    {
        $request->validate([
            'new_position' => 'required|string|max:255',
            'new_end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $new_position = $request->input('new_position');
        $new_end_date = $request->input('new_end_date');

        $employee = $employeeService->promoteEmployee($employee_id, $new_position, $new_end_date);

        return $this->successResponse(
            ['employee' => $employee],
            __('messages.employee_promoted'),
            200
        );
    }
}
