<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;
use Response;
use App\Employee;
use App\City;
use App\Province;
use App\Country;
use App\Department;
use App\Division;
use App\Role;
use App\Shift;

class EmployeeManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = DB::table('employees AS emp')
        ->leftJoin('employee_setup AS empset', 'emp.id', '=', 'empset.employee_id')
        ->leftJoin('city AS city', 'emp.city_id', '=', 'city.id')
        // ->leftJoin('department AS dept', 'empset.department_id', '=', 'dept.id')
        ->leftJoin('province AS province', 'emp.province_id', '=', 'province.id')
        ->leftJoin('country AS country', 'emp.country_id', '=', 'country.id')
        // ->leftJoin('division AS div', 'empset.division_id', '=', 'div.id')
        ->select('emp.*', 'country.id as country_id', 'province.id as province_id', 'city.id as city_id')
        ->paginate(5);
        
        return view('employees-mgmt/index', ['employees' => $employees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('employees-mgmt/create', ['countries' => $countries]);
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
        // Upload image
        $path = $request->file('picture')->store('avatars');
        $keys = ['employee_number','lastname', 'firstname', 'middlename', 'address', 'city_id', 'province_id', 'country_id', 'zip',
        'age', 'birthdate'];
        $input = $this->createQueryInput($keys, $request);
        $input['picture'] = $path;

        $employee = Employee::create($input);

        // Upon saving of employee details setup should also be saved
        $departments = Department::all();
        $divisions = Division::all();
        $roles = Role::all();
        $shifts = Shift::all();
        $approvers = Employee::all();/*DB::table('employees AS emp')
                    ->leftJoin('employee_setup as empset', 'emp.id', '=', 'emp_set.employee_id')
                    ->leftJoin('roles', 'role.id', '=', 'emp_set.role_id')
                    // ->where('role.name', '=', 'Supervisor')
                    ->select('emp.*')->get();*/
        $reports_to = Employee::all();
        return view('employee-setup-mgmt/create', ['employee' => $employee, 'departments' => $departments, 'divisions' => $divisions, 'roles' => $roles, 'shifts' => $shifts, 'approvers' => $approvers, 'reports_to' => $reports_to]);
        // return redirect()->intended('/employee-management');
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
        $countries = Country::all();
        $provinces = Province::where('country_id', $employee['country_id'])->get();
        $cities = City::where('province_id',$employee['province_id'])->get();//find($employee['city_id']);
        return view('employees-mgmt/edit', ['employee' => $employee, 'cities' => $cities, 'provinces' => $provinces, 'countries' => $countries]);//, 'departments' => $departments, 'divisions' => $divisions]);
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
        'age', 'birthdate'];
        $input = $this->createQueryInput($keys, $request);
        if ($request->file('picture')) {
            $path = $request->file('picture')->store('avatars');
            $input['picture'] = $path;
        }

        Employee::where('id', $id)
            ->update($input);

        return redirect()->intended('/employee-management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         Employee::where('id', $id)->delete();
         return redirect()->intended('/employee-management');
    }

    /**
     * Search province from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $constraints = [
            'firstname' => $request['firstname']
            ];
        $employees = $this->doSearchingQuery($constraints);
        return view('employees-mgmt/index', ['employees' => $employees, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        // ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('province', 'employees.province_id', '=', 'province.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        // ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->select('employees.firstname as employee_name', 'employees.*');
        //,'department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(5);
    }

     /**
     * Load image resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function load($name) {
         $path = storage_path().'/app/avatars/'.$name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }

    private function validateInput($request) {
        $this->validate($request, [
            'lastname' => 'required|max:60',
            'firstname' => 'required|max:60',
            // 'country_id' => 'required',
            'birthdate' => 'required'
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

    public function import(Request $request)
    {
        $file = $request->file('import_file');
        if($file){
            $path = $file->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();

            $headers = array (
                'employee_number'   =>  'employee_number',
                'last_name'         =>  'lastname',
                'first_name'        =>  'firstname',
                'middle_name'       =>  'middlename',
                'nickname'          =>  'nickname',
                'date_of_birth'     =>  'birthdate'
            );
            // echo "<pre>";print_r($data);die("jere");
            if(!empty($data) && $data->count()){
                foreach ($data as $employee) {
                    foreach ($employee as $details) {
                        foreach ($details as $key => $value) {
                            $arr[$headers[$key]] = $value;
                        }
                        $insert[] = $arr;
                    }
                    // 
                }
                echo "<pre>";print_r($insert);die("jere");

                // if(!empty($insert)){
                //     DB::table('items')->insert($insert);
                // //  dd('Insert Record successfully.');
                // }
            }
        } 
    }
}
