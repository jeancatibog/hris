<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\FormType;
use App\Employee;

class FormsController extends Controller
{
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
        $leave_forms = DB::table('employee_leaves AS el')
        ->leftJoin('employees AS emp', 'el.employee_id', '=', 'emp.id')
        ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
        ->leftJoin('form_status AS fs', 'el.form_status_id', '=', 'fs.id')
        ->where('emp.id','=',Auth::user()->employee_id)
        ->select('el.id','CONCAT(emp.firstname," ", emp.lastname) AS emp_name', 'ft.form', 'el.status', 'el.date_from', 'el.date_to')
        ->paginate(5);

        $forms['leaves'] = $leave_forms;
        return view('forms/index', ['forms' => $forms]);
    }

    /*
    * File form for leaves
    */
    public function create()
    {
    	// DB::enableQueryLog();
    	$form = $_GET['form'];
    	$employee_detail = Employee::find(Auth::user()->employee_id);
    	if($form == 'leave') {
    		$query = FormType::where('is_leave', 1);
    		if($employee_detail['gender'] == 1) {
				$query->where('for_men', 1);
			} else {
				$query->where('for_women', 1);
			}
    		$forms = $query->get();
    		// dd(DB::getQueryLog());		
    		return view('forms/'.$form.'/create', ['forms' => $forms, 'employee_id' => Auth::user()->employee_id]);
    	} else {
    		return view('forms/'.$form.'/create', ['employee_id' => Auth::user()->employee_id]);
    	}
    }

    /**
     * Store a newly created form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
    	$status = $id == 0 ? 1 : 2;
    	echo "<pre>";print_r($request['form_type_id'] . " = " . $request['employee_id'] . " = " . $request['date_from'] . " = " . $request['date_to'] . " = " . $request['reason'] . " = " . $request['is_halfday']);die("here");
    	Shift::create([
            'name'	=> 	$request['name'],
            'start' => 	date("H:i",strtotime($request['start'])),
            'end'	=>	date("H:i",strtotime($request['end'])),
            'first_halfday_end' => 	date("H:i",strtotime($request['first_halfday_end'])),
            'second_halfday_start'	=>	date("H:i",strtotime($request['second_halfday_start']))
        ]);
        /*$this->validateInput($request);
         Shift::create([
            'name'	=> 	$request['name'],
            'start' => 	date("H:i",strtotime($request['start'])),
            'end'	=>	date("H:i",strtotime($request['end'])),
            'first_halfday_end' => 	date("H:i",strtotime($request['first_halfday_end'])),
            'second_halfday_start'	=>	date("H:i",strtotime($request['second_halfday_start']))
        ]);

        return redirect()->intended('system-management/shift');*/
    }
}
