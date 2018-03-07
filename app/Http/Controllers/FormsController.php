<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\FormType;
use App\Employee;
use App\EmployeeLeaves;
use App\EmployeeLeaveDates;
use App\EmployeeOvertime;
use DateTime;
use DatePeriod;
use DateInterval;

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
    	// DB::enableQueryLog();
        $leave_forms = DB::table('employee_leaves AS el')
        ->leftJoin('employees AS emp', 'el.employee_id', '=', 'emp.id')
        ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
        ->leftJoin('form_status AS fs', 'el.form_status_id', '=', 'fs.id')
        ->where('emp.id','=',Auth::user()->employee_id)
        ->select('el.id', 'emp.firstname' , 'emp.lastname', 'ft.form', 'fs.status', 'el.date_from', 'el.date_to', 'el.reason')
        ->paginate(5);

        $ot_forms = DB::table('employee_overtime')
        ->where('employee_id', '=', Auth::user()->employee_id)->paginate(5); //EmployeeOvertime::where('employee_id', '=', Auth::user()->employee_id)->take(5)->get();
    	// dd(DB::getQueryLog());
        $forms['leaves'] = $leave_forms;
        $forms['ot'] = $ot_forms;
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
    	$this->validateInput($request, $request['ftype']);

    	$status = $id == 0 ? 1 : 2;
    	if ($request['ftype'] == 'leave') {
	    	/* Insert data for leave forms and dates */
	    	$leaves = EmployeeLeaves::create([
	            'employee_id'	=> 	$request['employee_id'],
	            'form_type_id'	=> 	$request['form_type_id'],
	            'date_from'		=> 	date('Y-m-d', strtotime($request['date_from'])),
	            'date_to'		=> 	date('Y-m-d', strtotime($request['date_to'])),
	            'reason'		=> 	$request['reason'],
	    		'is_halfday'	=>	$request['is_halfday'],
	    		'halfday_type'	=>	$request['halfday_type'],
	            'form_status_id'=>	$status
	        ]);
	        
	        $insertId = $leaves->id;
	        $dates = $this->getDatesFromRange($request['date_from'], $request['date_to']);
	        $credit = $request['is_halfday'] ? '0.5' : '1';
	        foreach ($dates as $date) {
	    		EmployeeLeaveDates::create([
	    			'employee_leave_id'	=>	$insertId,
	    			'date'				=>	$date,
	    			'leave_credit'		=>	$credit

	    		]);
	    	}	
	    	/* End insertion for leave and dates */
	    } elseif ($request['ftype'] == 'ot') {
	    	EmployeeOvertime::create([
	    		'employee_id'	=>	$request['employee_id'],
	    		'date'			=>	date('Y-m-d', strtotime($request['date'])),
	    		'datetime_from'	=>	date('Y-m-d H:i:s', strtotime($request['datetime_from'])),
	    		'datetime_to'	=>	date('Y-m-d H:i:s', strtotime($request['datetime_to'])),
	    		'reason'		=>	$request['reason'],
	            'form_status_id'=>	$status
	    	]);
	    }

        // return redirect()->intended('forms');
    }

    public function getDatesFromRange($start, $end) {
	    $array = array();
	    $interval = new DateInterval('P1D');

	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);

	    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	    foreach($period as $date) { 
	        $array[] = $date->format('Y-m-d'); 
	    }

	    return $array;
	}

    private function validateInput($request, $type) {
    	if($type == 'leave') {
    		$this->validate($request, [
	        	'form_type_id'	=>	'required',
	        	'date_from'		=>	'required|date',
	            'date_to'		=>	'required|date|after_or_equal:date_from',
	            'reason'		=>	'required'
	        ]);
    	} elseif($type == 'ot') {
    		$this->validate($request, [
	        	'date'	=>	'date',
	        	'datetime_from'		=>	'required|date|before:datetime_to',
	            'datetime_to'		=>	'required|date|after_or_equal:date_from',
	            'reason'		=>	'required'
	        ]);
    	}
    }
}
