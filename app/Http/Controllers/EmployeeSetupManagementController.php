<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\Employee;
use App\EmployeeSetup;
use App\Department;
use App\Division;
use App\Shift;
use App\Role;

class EmployeeSetupManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateInput($request);
        $keys = ['employee_id', 'date_hired', 'department_id', 'division_id', 'shift_id', 'role_id', 'position', 'job_title', 'reports_to_id', 'approver_id'];
        $input = $this->createQueryInput($keys, $request);

        EmployeeSetup::create($input);

        // Upon saving of employee details setup should also be saved
        return redirect()->intended('/employee-management');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($employee == null || count($employee) == 0) {
            return redirect()->intended('/employee-management');
        }

        return view('employees-setup-mgmt/edit', ['employee' => $employee]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->validateInput($request);
        // Upload image
        $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'province_id', 'country_id', 'zip',
        'age', 'birthdate'];//, 'date_hired', 'department_id', 'department_id', 'division_id'];
        $input = $this->createQueryInput($keys, $request);
        if ($request->file('picture')) {
            $path = $request->file('picture')->store('avatars');
            $input['picture'] = $path;
        }

        Employee::where('id', $id)
            ->update($input);

        return redirect()->intended('/employee-management');
    }

    private function validateInput($request) {
        $this->validate($request, [
            'date_hired' => 'required',
            'department_id' => 'required',
            'role_id'   => 'required',
            'approver_id'   =>  'required',
            'reports_to_id' =>  'required'
        ]);
    }

    private function createQueryInput($keys, $request) {
        $queryInput = [];
        for($i = 0; $i < sizeof($keys); $i++) {
            $key = $keys[$i];
            $queryInput[$key] = $request[$key];
        }

        return $queryInput;
    }
}
