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

        $form_status = [
            ['status' => 'Draft', 'description' =>  'Save as draft'],
            ['status' => 'For Approval', 'description' => 'Waiting for approvers approval'],
            ['status' => 'Approved', 'description' => 'Approved by approver'],
            ['status' => 'Disapproved', 'description' => 'Disapproved by approver'],
            ['status' => 'Cancel', 'description' => 'Cancelled by approver/employee']
        ];
        DB::table('form_status')->insert($form_status);
    }
}
