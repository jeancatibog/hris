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
        $employee = DB::table('hr_employees')->insert(
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
    }
}
