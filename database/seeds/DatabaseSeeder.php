<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee = DB::table('employees')->insert(
            array(
                'employee_number' => '2180002',
                'firstname' =>  'Jean',
                'lastname'  =>  'Catibog',
                'middlename'    =>  'Razon',
                'birthdate' =>  '1992-04-27',
            )
        );
        $emp_id = DB::getPdo()->lastInsertId();

        $user = factory(App\User::class)->create([
            'username' => 'admin',
            'email' => 'jeancatibog@gmail.com',
            'password' => bcrypt('admin'),
            'lastname' => 'Ms',
            'firstname' => 'admin',
            'employee_id'   => $emp_id
        ]);
        
        $roles = [
            ['name' => 'Super Administrator'],
            ['name' => 'HR Admin'],
            ['name' => 'Manager'],
            ['name' => 'Supervisor'],
            ['name' => 'Employee']
        ];
        
        DB::table('roles')->insert($roles);

        // Data insertion for Form status
        $form_status = [
            ['status' => 'Draft', 'description' =>  'Save as draft'],
            ['status' => 'For Approval', 'description' => 'Waiting for approvers approval'],
            ['status' => 'Approved', 'description' => 'Approved by approver'],
            ['status' => 'Disapproved', 'description' => 'Disapproved by approver'],
            ['status' => 'Cancel', 'description' => 'Cancelled by approver/employee']
        ];
        DB::table('form_status')->insert($form_status);

        // Data insertion for Form Types
        $form_type = [
            ['code' => 'OT', 'form' => 'Overtime', 'is_leave' => 0, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'OBT', 'form' => 'Official Business Trip', 'is_leave' => 0, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'VL', 'form' => 'Vacation Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'SL', 'form' => 'Sick Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'EL', 'form' => 'Emergency Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'BiL', 'form' => 'Birthday Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'SPL', 'form' => 'Single Parent Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 1],
            ['code' => 'PL', 'form' => 'Paternity Leave', 'is_leave' => 1, 'for_women' => 0, 'for_men' => 1],
            ['code' => 'ML', 'form' => 'Maternity Leave', 'is_leave' => 1, 'for_women' => 1, 'for_men' => 0]
        ];

        DB::table('form_type')->insert($form_type);

        // Data insertion for Period status
        $period_status = [
            ['status' => 'Processed', 'description' =>  'Period cover already processed'],
            ['status' => 'Closed', 'description' => 'Period cover transaction closed']
        ];
        DB::table('tk_period_status')->insert($period_status);

        $emp_status = [
            ['status' => 'Probationary', 'is_active' => 1 ],
            ['status' => 'Contractual/Project-based', 'is_active' => 1 ],
            ['status' => 'Regular', 'is_active' => 1 ],
            ['status' => 'Resigned', 'is_active' => 0 ],
            ['status' => 'Terminated', 'is_active' => 0 ]
        ];
        DB::table('employment_status')->insert($emp_status);
    }
}
