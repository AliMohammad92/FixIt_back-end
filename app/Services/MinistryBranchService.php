<?php

namespace App\Services;

use App\DAO\MinistryBranchDAO;
use Illuminate\Support\Facades\Cache;

class MinistryBranchService
{
    public function __construct(
        protected MinistryBranchDAO $dao
    ) {}

    public function store(array $data)
    {
        $branch = $this->dao->store($data);
        Cache::forget("all_branches");
        Cache::forget("branches_for_ministry {$data['ministry_id']}");
        return $branch;
    }

    public function read()
    {
        $cacheKey = "all_branches";
        $branches = Cache::remember($cacheKey, 86400, function () {
            return $this->dao->read();
        });
        return $branches;
    }

    public function readOne($id)
    {
        $cacheKey = "Branch {$id}";
        $branch = Cache::remember($cacheKey, 86400, function () use ($id) {
            return $this->dao->readOne($id);
        });
        return $branch;
    }

    public function assignManager($id, $manager_id, EmployeeService $employeeService)
    {
        $emp = $employeeService->readOne($manager_id);
        Cache::forget("Branch {$id}");
        $ministry = $this->dao->assignManager($id, $manager_id);

        $emp->user->syncRoles(['employee', 'branch_manager']);

        return $ministry;
    }
}
