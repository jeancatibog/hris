<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use DateTime;
use DatePeriod;
use DateInterval;
use App\FormType;
use App\Employee;
use App\EmployeeLeaves;
use App\EmployeeLeaveDates;
use App\EmployeeOvertime;
use App\EmployeeObt;
use App\CompanyPolicy;
use App\EmployeeDtrp;
use App\Http\Controllers\TimekeepingController;

class FormApprovalController extends Controller
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
        $employee_id = Auth::user()->employee_id;
        $employee_details = DB::table('employees AS e')
                ->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
                ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
                ->where('es.employee_id', $employee_id)
                ->select('es.team_id', 'r.name AS role','es.*')->get()->first();

        $acct_sched = DB::table('accounts_team_lead AS tl')
            ->leftJoin('account AS a', 'tl.account_id', '=', 'a.id')
            ->where('tl.account_id', $employee_details->account_id)
            ->where('tl.team_lead_id', $employee_details->employee_id)
            ->get()->toArray();

        // DB::enableQueryLog();
        $leave_form = DB::table('employee_leaves AS el')
            ->leftJoin('employees AS e', 'el.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'el.employee_id', '=', 'es.employee_id')
            ->leftJoin('form_status AS fs', 'el.form_status_id', '=', 'fs.id')
            ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->where(function ($query) use ($acct_sched, $employee_details, $employee_id) {
                if (count($acct_sched) > 0) {
                    foreach ($acct_sched as $sched) {
                        $query->orWhereBetween('el.date_from',[$sched->date_from, $sched->date_to]);
                    }

                    $query->where('es.account_id', $employee_details->account_id);
                } else {
                    $query->where('es.approver_id', $employee_id);
                }
            })
            ->where('fs.status', 'For Approval')
            ->where('es.team_id', $employee_details->team_id)
            ->where('el.employee_id', '!=', $employee_id); //exclude own filed forms
            if ($employee_details->role == 'Team Lead') {
                $leave_form->whereNotIn('r.name', ['Team Lead', 'Supervisor', 'Manager']); //exclude all user with roles higher than associate
            } elseif ($employee_details->role == 'Supervisor') {
                $leave_form->where('r.name', 'Team Lead');
            } elseif ($employee_details->role == 'Manager') {
                $leave_form->where('r.name', 'Supervisor');
            }
            $leave_form->select(DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'), 'el.id', 'ft.form', 'fs.status', 'el.date_from', 'el.date_to', 'el.reason');

            $leave = $leave_form->get()->toArray();
        // echo "<pre>";print_r($leave);die("here");
        // echo "<pre>";print_r($acct_sched);die("here");
        // echo "<pre>";print_r(DB::getQueryLog());die("jere");

        $ot_forms = array();
        $obt_forms = array();
        // DB::enableQueryLog();
        // $ot_forms = DB::table('employee_overtime AS ot')
	       //  ->leftJoin('form_status AS fs', 'ot.form_status_id', '=', 'fs.id')
        //     ->leftJoin('employee_workschedule AS ew', function($join)
        //         {
        //             $join->on('ot.date', '=', 'ew.date');
        //             $join->on('ot.employee_id','=','ew.employee_id');
        //         })
        //     ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
        //     ->where('ot.employee_id', '=', Auth::user()->employee_id)
	       //  ->select('s.is_restday','ot.*', 'fs.status')->paginate(5); 

        // $obt_forms = DB::table('employee_obt AS obt')
	       //  ->leftJoin('form_status AS fs', 'obt.form_status_id', '=', 'fs.id')
	       //  ->where('employee_id', '=', Auth::user()->employee_id)
	       //  ->select('obt.*', 'fs.status')->paginate(5); 
        // dd(DB::getQueryLog());
        $approval_form['leaves'] = $leave;
        $approval_form['ot'] = $ot_forms;
        $approval_form['obt'] = $obt_forms;
        return view('form-approval/index', ['form_approval' => $approval_form]);
    }

    /*
    * File form for leaves
    */
    /*public function create()
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
    }*/

    /**
     * Store a newly created form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $returnHTML = "";
        $form = $request['form'];
        if($form == 'leave') {
            $leave = EmployeeLeaves::find($id);
            
            $employee_detail = Employee::find(Auth::user()->employee_id);
            
            $query = FormType::where('is_leave', 1);
            if($employee_detail['gender'] == 1) {
                $query->where('for_men', 1);
            } else {
                $query->where('for_women', 1);
            }
            $types = $query->get();
            $file_form['leave'] = $leave;
            $params = ['form' => $file_form[$form], 'types' => $types];
        } elseif($form == 'overtime') {
            $ot = EmployeeOvertime::find($id);

            $file_form['overtime'] = $ot;

            $params = ['form' => $file_form[$form]];
        } elseif($form == 'obt') {
            $obt = EmployeeObt::find($id);
            $obt['starttime'] = date('h:i A', strtotime($obt['starttime']));
            $obt['endtime'] = date('h:i A', strtotime($obt['endtime']));

            $file_form['obt'] = $obt;

            $params = ['form' => $file_form[$form]];
        }
        $returnHTML = view('forms.'.$form.'.edit')->with($params)->render();
        // echo "<pre>";print_r(view('forms.'.$form.'.edit')->with($params)->render());die("jere");
        return response()->json( array('html'=>$returnHTML) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id=0, $action_id=0)
    {
        $this->validateInput($request, $request['ftype']);
        $status = $_GET['action_id'] == 0 ? 1 : 2;
    	if ($request['ftype'] == 'leave') {
    		/*Delete existing dates on employee leave dates*/
    		EmployeeLeaveDates::where('employee_leave_id', $id)->delete();
	    	/* Update data for leave forms and dates */
	    	$input = [
	            'employee_id'	=> 	$request['employee_id'],
	            'form_type_id'	=> 	$request['form_type_id'],
	            'date_from'		=> 	date('Y-m-d', strtotime($request['date_from'])),
	            'date_to'		=> 	date('Y-m-d', strtotime($request['date_to'])),
	            'reason'		=> 	$request['reason'],
	    		'is_halfday'	=>	$request['is_halfday'],
	    		'halfday_type'	=>	$request['halfday_type'],
	            'form_status_id'=>	$status
	        ];

	        EmployeeLeaves::where('id', $id)
	            ->update($input);
	        $this->storeLeaveDates($id, $request['is_halfday'], $request['date_from'], $request['date_to']);
	    	/* End insertion for leave and dates */
	    } elseif ($request['ftype'] == 'ot') {
	    	$input = [
	            'employee_id'	=>	$request['employee_id'],
	    		'date'			=>	date('Y-m-d', strtotime($request['date'])),
	    		'datetime_from'	=>	date('Y-m-d H:i:s', strtotime($request['datetime_from'])),
	    		'datetime_to'	=>	date('Y-m-d H:i:s', strtotime($request['datetime_to'])),
	    		'reason'		=>	$request['reason'],
	            'form_status_id'=>	$status
	        ];
	        EmployeeOvertime::where('id', $id)
	            ->update($input);
	    } elseif ($request['ftype'] == 'obt') {
            $input = [
                'employee_id'       =>  $request['employee_id'],
                'date_from'         =>  date('Y-m-d', strtotime($request['date_from'])),
                'date_to'           =>  date('Y-m-d', strtotime($request['date_to'])),
                'starttime'         =>  date('H:i:s', strtotime($request['starttime'])),
                'endtime'           =>  date('H:i:s', strtotime($request['endtime'])),
                'reason'            =>  $request['reason'],
                'form_status_id'    =>  $status,
                'contact_name'      =>  $request['contact_name'],
                'contact_info'      =>  $request['contact_info'],
                'contact_position'  =>  $request['contact_position'],
                'company_to_visit'  =>  $request['company_to_visit'],
                'company_location'  =>  $request['company_location']
            ];
            EmployeeObt::where('id', $id)
                ->update($input);
        }
        
        return redirect()->intended('forms');
    }

    public function storeLeaveDates($id, $is_halfday, $from, $to)
    {
    	$dates = $this->getDatesFromRange($from, $to);
        $credit = $is_halfday ? '0.5' : '1';
        foreach ($dates as $date) {
    		EmployeeLeaveDates::create([
    			'employee_leave_id'	=>	$id,
    			'date'				=>	$date,
    			'leave_credit'		=>	$credit

    		]);
    	}	
    }

    private function validateInput($request, $type) 
    {
        $tkController = new TimekeepingController;
        $prepost = '';
    	$policy = CompanyPolicy::find(1); // This is now static to 1 policy
        $date = $request['date'];
        $holidays = $tkController->getHoliday($date);
        $holiday = (count($holidays) > 0) ? 1 : 0;
        /* check for employee shift */
        $empShift = DB::table('employee_workschedule AS ew')
                ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
                ->where('ew.employee_id', Auth::user()->employee_id)
                ->where('ew.date', $request['date'])
                ->select('s.start', 's.end', 's.is_restday')->first();
        if (count($empShift) == 0 ) {
            $empShift = DB::table('employee_setup AS es')
                    ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
                    ->where('es.employee_id', '=', Auth::user()->employee_id)
                    ->select('s.end','s.start', 's.is_restday')->first();
        }

        if(strtotime($empShift->start) > strtotime($empShift->end)) {
            $dateEnd = date("Y-m-d" , date(strtotime("+1 day", strtotime($date))));
        } else {
            $dateEnd = $date;
        }
        $restday = $empShift->is_restday;
        $shiftStart = $date . " " . $empShift->start;
        $shiftEnd = $dateEnd . " " . $empShift->end;
    	if($type == 'leave') {
    		$this->validate($request, [
	        	'form_type_id'	=>	'required',
	        	'date_from'		=>	'required|date',
	            'date_to'		=>	'required|date|after_or_equal:date_from',
	            'reason'		=>	'required'
	        ]);
    	} elseif($type == 'ot') {
    		$message = [
    			'min_ot' => 'The overtime filing must be greater than or equal ' . $policy['min_ot'] . ' minutes',
    			'pre_post_ot' => 'The overtime filing must be before start of shift or after end of shift'
    		];
            if (!$holiday && !$restday) {
                $prepost = '|pre_post_ot:'.date("Y-m-d H:i:s",strtotime($request['datetime_from'])).','.$shiftStart.','.$shiftEnd;
            }
    		$this->validate($request, [
	        	'date'			=>	'date',
	        	'datetime_from'	=>	'required|date|before:datetime_to',
	            'datetime_to'	=>	'required|date|after_or_equal:datetime_from|min_ot:'.$request['datetime_from'] .','.$policy['min_ot']. $prepost,
	            'reason'		=>	'required'
	        ], $message);
    	}
    }
}
