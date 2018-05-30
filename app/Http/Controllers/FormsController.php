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
use App\EmployeeOfd;
use App\Http\Controllers\TimekeepingController;

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
        $role = DB::table('employee_setup AS es')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->where('es.employee_id', Auth::user()->employee_id)
            ->select('r.name AS role')->first();

        $leave_forms = DB::table('employee_leaves AS el')
	        ->leftJoin('employees AS emp', 'el.employee_id', '=', 'emp.id')
	        ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
	        ->leftJoin('form_status AS fs', 'el.form_status_id', '=', 'fs.id')
	        ->where('emp.id','=',Auth::user()->employee_id)
	        ->select('el.id', 'emp.firstname' , 'emp.lastname', 'ft.form', 'fs.status', 'el.date_from', 'el.date_to', 'el.reason')
	        ->paginate(5);

        // DB::enableQueryLog();
        $ot_forms = DB::table('employee_overtime AS ot')
	        ->leftJoin('form_status AS fs', 'ot.form_status_id', '=', 'fs.id')
            ->leftJoin('employee_workschedule AS ew', function($join)
                {
                    $join->on('ot.date', '=', 'ew.date');
                    $join->on('ot.employee_id','=','ew.employee_id');
                })
            ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
            ->where('ot.employee_id', '=', Auth::user()->employee_id)
	        ->select('s.is_restday','ot.*', 'fs.status')->paginate(5); 

        $obt_forms = DB::table('employee_obt AS obt')
	        ->leftJoin('form_status AS fs', 'obt.form_status_id', '=', 'fs.id')
	        ->where('employee_id', '=', Auth::user()->employee_id)
	        ->select('obt.*', 'fs.status')->paginate(5); 

        $ofd_forms = DB::table('employee_ofd AS ofd')
            ->leftJoin('form_status AS fs', 'ofd.form_status_id', '=', 'fs.id')
            ->where('employee_id', '=', Auth::user()->employee_id)
            ->select('ofd.*', 'fs.status')->paginate(5); 
        // echo "<pre>";print_r($role->role);die("sda");
        // dd(DB::getQueryLog());
        $forms['leaves'] = $leave_forms;
        $forms['ot'] = $ot_forms;
        $forms['obt'] = $obt_forms;
        $forms['ofd'] = $ofd_forms;
        $forms['role'] = $role->role;
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
	        $this->storeLeaveDates($insertId, $request['is_halfday'], $request['date_from'], $request['date_to']);	
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
	    } elseif ($request['ftype'] == 'obt') {
            EmployeeObt::create([
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
            ]);

        } elseif ($request['ftype'] == 'ofd') {
            EmployeeOfd::create([
                'employee_id'   =>  $request['employee_id'],
                'date'          =>  date('Y-m-d', strtotime($request['date'])),
                'start'         =>  date('Y-m-d H:i:s', strtotime($request['start'])),
                'end'           =>  date('Y-m-d H:i:s', strtotime($request['end'])),
                'reason'        =>  $request['reason'],
                'form_status_id'=>  $status,
            ]);
        } else {
            EmployeeDtrp::create([
                'employee_id'   =>  $request['employee_id'],
                'date'          =>  $request['date'],
                'log_type_id'   =>  $request['log_type'],
                'timelog'       =>  date('Y-m-d H:i:s', strtotime($request['timelog'])),
                'reason'        =>  $request['reason'],
                'form_status_id'=>  $status    
            ]);
        }

        return redirect()->intended('forms');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($form="")
    {
    	$id = $_GET['id'];
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
        } elseif($form == 'ofd') {
            $ofd = EmployeeOfd::find($id);
            $ofd['start'] = date('Y-m-d h:i A', strtotime($ofd['start']));
            $ofd['end'] = date('Y-m-d h:i A', strtotime($ofd['end']));

            $file_form['ofd'] = $ofd;

            $params = ['form' => $file_form[$form]];
        }
        
        return view('forms/'.$form.'/edit', $params);
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
        } elseif ($request['ftype'] == 'ofd') {
            $input = [
                'employee_id'       =>  $request['employee_id'],
                'date'              =>  date('Y-m-d', strtotime($request['date'])),
                'start'             =>  date('Y-m-d H:i:s', strtotime($request['start'])),
                'end'               =>  date('Y-m-d H:i:s', strtotime($request['end'])),
                'reason'            =>  $request['reason'],
                'form_status_id'    =>  $status
            ];
            EmployeeOfd::where('id', $id)
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

    public function getDatesFromRange($start, $end)
    {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $oneDay = new DateInterval('P1D');
	    $period = new DatePeriod(
                $start,
                $oneDay,
                $end->add($oneDay)
        );
        $all_days = array();$i = 0;
        
        foreach($period as $date) {
            // check in workschedule setup if restday
            $empShift = DB::table('employee_workschedule AS ew')
                ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
                ->where('ew.employee_id', Auth::user()->employee_id)
                ->where('ew.date', $date)
                ->select('s.start', 's.end', 's.is_restday')->first();
            if (count($empShift) > 0) {
                $restday = $empShift->is_restday;
                if(!$restday) {
                    $all_days[$i] = $date->format('Y-m-d');
                    $i++;
                }
            } else {
                if ($this->isWeekend($date->format('Y-m-d'))){
                    $all_days[$i] = $date->format('Y-m-d');
                    $i++;
                }
            }
        }
        return $all_days;
    }

    public function isWeekend($date) 
    {
        $weekDay = date('w', strtotime($date));
        if (($weekDay == 0 || $weekDay == 6)){
            return false;
        }else{
            return true;
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
        $empCredits = DB::table('employee_leave_credits')
            ->where('employee_id', Auth::user()->employee_id)
            ->where('leave_type_id', $request['form_type_id'])
            ->where('year', date('Y', strtotime($request['date_from'])))
            ->get()->first();

        $diff = ((strtotime($request['date_to']) - strtotime($request['date_from']))/(60*60*24))+1;
        $days = $request['is_halfday'] ? $diff * 0.5 : $diff;

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
            $message = [
                'no_credit' => 'Leave credits is not enough'
            ];
    		$this->validate($request, [
	        	'form_type_id'	=>	'required',
	        	'date_from'		=>	'required|date',
	            'date_to'		=>	'required|date|after_or_equal:date_from|no_credit:'.$empCredits->balance.','.$days,
	            'reason'		=>	'required'
	        ], $message);
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
