<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\Employee;
use App\City;
use App\Province;
use App\Country;
// use App\Department;
// use App\Division;

class EmployeeManagementController extends Controller
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
    public function index()
    {
        // DB::enableQueryLog();
        $employees = DB::table('employees AS emp')
        ->leftJoin('employee_setup AS empset', 'emp.id', '=', 'empset.employee_id')
        ->leftJoin('city AS city', 'emp.city_id', '=', 'city.id')
        // ->leftJoin('department AS dept', 'empset.department_id', '=', 'dept.id')
        ->leftJoin('province AS province', 'emp.province_id', '=', 'province.id')
        ->leftJoin('country AS country', 'emp.country_id', '=', 'country.id')
        // ->leftJoin('division AS div', 'empset.division_id', '=', 'div.id')
        ->select('emp.*', 'country.id as country_id', 'province.id as province_id', 'city.id as city_id')
        ->paginate(5);
        // echo "<pre>"; print_r(DB::getQueryLog());die("ere");
        return view('employees-mgmt/index', ['employees' => $employees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $cities = City::all();
        // $states = State::all();
        $countries = Country::all();
        return view('employees-mgmt/create', ['countries' => $countries]);//, 'departments' => $departments, 'divisions' => $divisions*/]);
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
        $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'province_id', 'country_id', 'zip',
        'age', 'birthdate'];//, 'date_hired', 'department_id', 'division_id'];
        $input = $this->createQueryInput($keys, $request);
        $input['picture'] = $path;

        $employee = Employee::create($input);


        $departments = Department::all();
        $divisions = Division::all();
        // Upon saving of employee details setup should also be saved
        return view('employee-setup-mgmt/create', ['employee_id' => $employee->id]);
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
