<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\Employee;
use App\Department;
use App\Division;
// use App\Shift;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function index()
    {
        // DB::enableQueryLog();
        $employees = DB::table('employees AS emp')
        ->leftJoin('employee_setup AS empset', 'emp.id', '=', 'empset.employee_id')
        ->leftJoin('department AS dept', 'empset.department_id', '=', 'dept.id')
        ->leftJoin('division AS div', 'empset.division_id', '=', 'div.id')
        ->select('emp.*', 'dept.id as department_id', 'div.id as division_id')
        ->paginate(5);
        // echo "<pre>"; print_r(DB::getQueryLog());die("ere");
        return view('employee-setup-mgmt/index', ['employees' => $employees]);
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        return view('employee-setup-mgmt/create', ['employees' => $countries]);
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateInput($request);
        $keys = ['department_id', 'division_id', 'shift_id', 'report_to', 'approver_id'];//, 'date_hired', 'department_id', 'division_id'];
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

        return view('employees-setup-mgmt/edit', ['employee' => $employee);$divisions]);
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
            'lastname' => 'required|max:60',
            'firstname' => 'required|max:60',
            'middlename' => 'required|max:60',
            'address' => 'required|max:120',
            'country_id' => 'required',
            'zip' => 'required|max:10',
            'age' => 'required',
            'birthdate' => 'required'//,
            // 'date_hired' => 'required',
            // 'department_id' => 'required',
            // 'division_id' => 'required'
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
