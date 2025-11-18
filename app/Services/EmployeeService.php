<?php

namespace App\Services;

use App\DAO\EmployeeDAO;
use App\Http\Requests\BaseUserRequest;
use App\Models\User;
use App\Models\UserOTP;
use Illuminate\Support\Facades\Auth;

class EmployeeService
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new EmployeeDAO();
    }

    public function add($user, OTPService $otpService)
    {
        $user->assignRole('employee');
        $otpService->resendOTP($user->id);
    }

    public function promoteEmployee($employee_id, $new_position, $new_end_date = null)
    {
        $employee = $this->dao->findById($employee_id);
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

        if (!isset($promotionRules[$new_position])) {
            throw new \Exception(__('messages.invalid_promotion_position'), 403);
        }

        $allowedRoles = $promotionRules[$new_position]['allowed_roles'];
        if (!$user->hasAnyRole($allowedRoles)) {
            throw new \Exception(__('messages.unauthorized_promotion'), 403);
        }

        $updatedEmployee = $this->dao->updatePosition($employee, $new_position, $new_end_date);
        $updatedEmployee->user->syncRoles($promotionRules[$new_position]['sync_roles']);

        return $updatedEmployee;
    }
}
