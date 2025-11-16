<?php

namespace App\DAO;

use App\Models\Employee;
use App\Models\User;

class EmployeeDAO
{
    public function add($data, $dataUser)
    {
        $user = User::create($dataUser);
        $employee = $user->employee()->create([
            'position' => $data['position'],
            'start_date' => $data['start_date'],
            'ministry_branch_id' => $data['branch_id'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);

        return $user;
    }

    public function findById($id)
    {
        return Employee::find($id);
    }

    public function getAll()
    {
        return Employee::all();
    }

    public function updatePosition($employee, $new_position, $new_end_date = null)
    {
        $employee->position = $new_position;
        if ($new_end_date) {
            $employee->end_date = $new_end_date;
        }
        $employee->save();
        return $employee;
    }

    public function getEmployeesInBranch($branch_id)
    {
        return Employee::where('ministry_branch_id', $branch_id)->get();
    }
}
