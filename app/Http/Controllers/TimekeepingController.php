<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use DateTime;
use DatePeriod;
use DateInterval;
use App\RawLogs;
use App\TimekeepingPeriod;
use App\EmployeeOvertime;
use App\Employee;
use App\EmployeeDtrSummary;
use App\CompanyPolicy;
use App\Shift;
use App\Http\Controllers\FormsController;

class TimekeepingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        // $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }

    /*
    * Creation of timekeeping period coverage
    */
    public function index()
    {
        $periods = DB::table('tk_period AS tkp')
                ->leftJoin('tk_period_status AS tkps', 'tkp.status_id', '=', 'tkps.id')
                ->select('tkp.*', 'tkps.status')
                ->paginate(5);
        return view('timekeeping/period/index', ['periods' => $periods]);
    }

    /*
	* Logs all in and  out of employee tru bundy clock on dashboard
    */
    public function log(Request $request)
    {
        $input = [
        	'employee_id'	=>	$request['employee_id'],
        	'date'			=>	$request['date'],
        	'checktime'		=>	$request['checktime'],
        	'checktype'		=>	$request['ctype'],
        	'processed'		=>	0
        ];

        try {
        	RawLogs::create($input);

        	return redirect('/dashboard')->with([
        		'status'	=>	'success', 
        		'message'	=>	"You have succesfuly " . str_replace("_", " ", $request['ctype']) ." at ". date('h:i A', strtotime($request['checktime'])) ]);

        } catch (\Exception $e) {
        	return redirect('/dashboard')->with([
        		'status'	=>	'error', 
        		'message'	=>	"Something went wrong!"
        	]);
        }
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

    public function create()
    {
        return view('timekeeping/period/create');
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
        TimekeepingPeriod::create([
            'start_date' =>  date("Y-m-d",strtotime($request['start_date'])),
            'end_date'   =>  date("Y-m-d",strtotime($request['end_date'])),
            'status_id'  =>  1
        ]);

        return redirect()->intended('timekeeping/period');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $period = TimekeepingPeriod::find($id);
        return view('timekeeping/period/edit', ['period' => $period]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $_GET['id'];
        $input = [
            'start_date' =>  date("Y-m-d",strtotime($request['start_date'])),
            'end_date'   =>  date("Y-m-d",strtotime($request['end_date']))
        ];
        TimekeepingPeriod::where('id', $id)
            ->update($input);
        return redirect()->intended('timekeeping/period');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shift::where('id', $id)->delete();
         return redirect()->intended('system-management/shift');
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'start_date' =>  'required',
            'end_date'   =>  'required|date|after_or_equal:start_date'
        ]);
    }

    public function process()
    {
        $periods = TimekeepingPeriod::where('status_id', 1)->orderBy('start_date', 'ASC')->get();
        return view('timekeeping/process/processing', ['periods' => $periods]);
    }

    public function processing(Request $request)
    {
        $formController = new FormsController;
        $periodId = $request['period_id'];

        $period = TimekeepingPeriod::find($periodId);
        $periodCover = $this->getDatesFromRange($period['start_date'], $period['end_date']);
        $employees = DB::table('employees AS e')
                        ->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
                        ->leftJoin('employment_status AS est', 'es.status_id', '=', 'est.id')
                        ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
                        ->where('est.is_active', 1)
                        ->select('e.id AS employee_id', 'es.shift_id', 's.start', 's.end')->get()->toArray(); /* GET ALL ACTIVE EMPLOYEES */
        // $total = 0;
        // $ctr = 0;
        // $percent = 0;
        foreach ($employees as $employee) {
            $summary = array();
            $empId = $employee->employee_id;
            foreach ($periodCover as $workdate) {
                $data = array();
                $day_type = '';
                $leave_type = '';
                $actualIn = $actualOut = 0;
                $absent = 0;
                $late = 0;
                $undertime = 0;
                $leave = 0;
                $work_hrs = 0;
                $otHrs = 0;
                $ot = 0;
                $otEx = 0;
                $ndiff = 0;
                $ndot_excess = 0;
                $holiday = 0;
                $brk = 1;
                $legot = 0;
                $legot_excess = 0;
                $leg_ndot = 0;
                $splot = 0;
                $splot_excess = 0;
                $spl_ndot = 0;
                $dobot = 0;
                $dobot_excess = 0;
                
                /* check if date is restday */
                $isWeekday = $formController->isWeekend($workdate);
                
                /* check if date is holiday */
                $holidays = $this->getHoliday($workdate);
                if (count($holidays) > 0) {
                    $absent = 0;
                    $leave = 0;
                    $holiday = 1;
                    if (!$isWeekday && $holidays->legal_holiday) {
                        $day_type = 'legrd';
                    } elseif (!$isWeekday && !$holidays->legal_holiday) {
                        $day_type = 'splrd';
                    } elseif ($holidays->legal_holiday) {
                        $day_type = 'leg';
                    } else {
                        $day_type = 'spl';
                    }
                } else {
                    $day_type = $isWeekday ? 'reg' : 'rd';
                }

                /* check if there is assigned shift by the supervisor or manager */
                $shift = $this->getShiftSchedule($empId, $workdate);
                if (count($shift) > 0) { 
                    $shiftId = $shift->shift_id;
                    $shiftIn = $shift->start; //shift time start
                    $shiftOut = $shift->end; // shift time end
                } else { //if no shift assigned get the default shift
                    $shiftId = $employee->shift_id;
                    $shiftIn = $employee->start; //shift time start
                    $shiftOut = $employee->end; // shift time end
                }

                $nextDayIn = date("Y-m-d" , date(strtotime("+1 day", strtotime($workdate)))) . " " . $shiftIn;
                $dtOut = strtotime($shiftIn) > strtotime($shiftOut) ? date("Y-m-d" , date(strtotime("+1 day", strtotime($workdate)))) : $workdate;
                $endShift = $oiTime = $dtOut . " " .$shiftOut;
                $startShift = $ooTime = $workdate . " " . $shiftIn;
                // echo "<pre>";print_r($empId."==".$workdate . " = " . $shiftIn."-".$shiftOut." == ".$dtOut);

                /* check if there is filed leave */
                $leaves = $this->getLeaves($empId, $workdate);

                $rawLogs = $this->getRawLogs($empId, $workdate, $dtOut, $nextDayIn, $leaves);
                // echo "<pre>";print_r($workdate);
                if (is_array($rawLogs) && count($rawLogs) > 0) {
                    // echo "<pre>";print_r($rawLogs);
                    foreach ($rawLogs as $log) {
                        $cType = $log['checktype'];
                        $cTime = $log['date']. " " . $log['checktime'];
                        if (strtotime($shiftIn) > strtotime($shiftOut)) { // NIGHT SHIFT EMPLOYEE
                            if ($cType == 'time_in') { //time in
                                $actualIn = strtotime($cTime) < strtotime($oiTime) ? strtotime($cTime) > strtotime($workdate . " " . $shiftOut ) ? date("Y-m-d H:i", strtotime($cTime)) : 0 : date("Y-m-d H:i", strtotime($oiTime));
                                $oiTime = $actualIn;
                            }
                            if ($cType == 'time_out') { //time out
                                $actualOut = strtotime($cTime) > strtotime($ooTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($ooTime));
                                $ooTime = $actualOut;
                            }
                        } else { // DAY SHIFT EMPLOYEE
                            /* GET THE EARLIEST TIME IN AND LATEST TIME OUT*/
                            if($cType == 'time_in') {
                                $actualIn = strtotime($cTime) < strtotime($oiTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oiTime));
                                $oiTime = $actualIn;
                            } else {
                                if (strtotime($cTime) > strtotime($ooTime)) {
                                    $actualOut =  date("Y-m-d H:i", strtotime($cTime));
                                }
                                $ooTime =  $actualOut;
                            }
                        }
                    }
                    // night differential for night shifts
                    if ($isWeekday && !$holiday) {
                        $ndiff = strtotime($shiftIn) > strtotime($shiftOut) ? $this->getNdiff($workdate, $actualIn, $actualOut) : 0;
                    }
                } else { //no logs from dtr
                    /* check if date is restday */
                    if ($isWeekday && !$holiday) {
                        $absent = 1;
                    }
                }

                // if no computed in and out
                if (empty($actualIn) && empty($actualOut) && (!$holiday && $isWeekday)) {
                    /* check if there is dtrp apporoved */
                    $absent = 1;
                }

                if(!empty($leaves)) {
                    if ($leaves->code == "LWOP") {
                        $absent = 1;
                    } else {
                        $absent = 0;
                    }
                    $leave = 1;
                    $leave_type = $leaves->form;

                    $halfday = $leaves->is_halfday;
                    $halfday_type = $leaves->halfday_type;
                    $shift = Shift::find($shiftId);
                    if ($halfday) {
                        if($halfday_type == 1){
                            $dt = strtotime($shiftIn) > strtotime($shiftOut) ? $dtOut : $workdate;
                            $startShift = $dt . " " . $shift->second_halfday_start;
                            $brk = 0;
                        } else {
                            $dt = strtotime($shiftIn) > strtotime($shiftOut) ? $dtOut : $workdate;
                            $endShift = $dt . " " . $shift->first_halfday_end;
                        }
                    }
                }

                if(!$absent) {
                    if(!$holiday && $isWeekday) { // compute lates only if not holiday and not restday
                        /* GET LATES HOURS */
                        if (strtotime($startShift) < strtotime($actualIn)) {
                           $late = $this->getTardinessHrs($startShift, $actualIn);
                        }

                        /* GET UNDERTIME HOURS */
                        if (strtotime($endShift) > strtotime($actualOut)) {
                            $undertime = $this->getTardinessHrs($actualOut, $endShift);
                        }
                    }    

                    /* GET HOURS WORKED */
                    $work_hrs = $this->getWorkHrs($actualIn, $actualOut);

                    /*GET OT HOURS FROM FILED FORMS*/
                    $otHrs = $this->getOtHrs($empId, $workdate, $actualOut, $startShift, $endShift);
                }
                $data = [
                    'period_id'     =>  $periodId,
                    'employee_id'   =>  $empId,
                    'shift_id'      =>  $shiftId,
                    'date'          =>  $workdate,
                    'day_type'      =>  $day_type,
                    'time_in'       =>  $actualIn,
                    'time_out'      =>  $actualOut,
                    'hours_work'    =>  $work_hrs,
                    'absent'        =>  $absent,
                    'late'          =>  $late,
                    'undertime'     =>  $undertime,
                    // 'ot_hours'        =>  $ot,
                    // 'ot_excess'     =>  $otEx,
                    'ndiff'         =>  $ndiff,
                    'leave'         =>  $leave,
                    'leave_type'    =>  $leave_type
                ];
                $merge = is_array($otHrs) ? array_merge($data, $otHrs) : $data;
                $summary[] = $merge;
            } //end period days loop 
            /* SAVE DTR SUMMARY */
            // $total = count($summary);
            // echo "<pre>";print_r($summary);
            foreach ($summary as $dtr) {
                // $ctr++;
                // $percent = intval($ctr/$total *100);
                /* CHECK IF RECORDS EXISTS (REPROCESSING) */
                $dtrExists = DB::table('tk_employee_dtr_summary')
                            ->where('period_id', $periodId)
                            ->where('employee_id', $empId)
                            ->where('date', $dtr['date'])
                            ->select('*')->get()->first();
                
                if (!empty($dtrExists)) {
                    //update
                    EmployeeDtrSummary::where('id', $dtrExists->id)
                                ->update($dtr);
                } else {
                    //add new record
                    EmployeeDtrSummary::create($dtr);
                }
                // $percentage[] = array('percent' => $percent);
            }
        } // end of employee loop
        // die("here");
        // echo json_encode($percentage);

        return response()->json(array('status' => 'success'));
    }

    /* GET SHIFT FROM EMPLOYEE WORKSCHEDULE */
    public function getShiftSchedule($empId, $date)
    {
        $shift = DB::table('employee_workschedule AS ew')
            ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
            ->where('ew.employee_id', $empId)
            ->where('ew.date', $date)
            ->select('ew.shift_id','s.start', 's.end')->get()->first();
        return $shift;

    }

    /* CHECK IF DATE IS HOLIDAY */
    public function getHoliday($date)
    {
        $holiday = DB::table('holiday')
                ->where('date_set', $date)
                ->get()->first();
        return $holiday;
    }

    /* GET RAW LOGS IN/OUT */
    public function getRawLogs($empId, $date, $dtOut, $nextDayIn, $leaves)
    {
        $qry = RawLogs::where('employee_id', $empId);

        if (strtotime($dtOut) > strtotime($date)) { // night shift
            if (!empty($leaves)) {
                $qry->whereBetween('date',[$date, $dtOut])
                    ->select('*');
            } else {
                $qry->where(function($query) use ($date, $dtOut)
                {
                    $query->where([['date', '=', $date], ['checktype', '=', 'time_in']])
                        ->orWhere([['date', '=', $dtOut], ['checktype', '=', 'time_out']]);
                })->select('*');
            }
            $rawLogs = $qry->get()->toArray();
        } else { // day shift
            // DB::enableQueryLog();
            $qry->where(function($query) use ($date, $nextDayIn) 
            {
                $query->where('date', $date)
                    ->orWhere(function($q) use ($nextDayIn)
                    {
                        $q->where('date', date("Y-m-d", strtotime($nextDayIn)))
                            ->where('checktype', 'time_out')
                            ->where('checktime', '<', date('H:i:s', strtotime($nextDayIn)));
                    });
            })->select('*');
            $rawLogs = $qry->get()->toArray();
            // echo "<pre>";print_r(DB::getQueryLog());
        }
        return $rawLogs;
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
            $all_days[$i] = $date->format('Y-m-d');
            $i++;
        }
        return $all_days;
    }

    /* OVERTIME COMPUTATION FROM FILED FORMS */
    public function getOtHrs($empId, $date, $actualOut, $startShift, $endShift)
    {
        $formController = new FormsController;
        $holidays = $this->getHoliday($date);
        if (count($holidays) > 0) {
            $holiday = 1;
            $legal = $holidays->legal_holiday;
        } else {
            $holiday = 0;
        }
        $restdays = $this->getShiftSchedule($empId, $date);
        if (count($restdays) > 0) {
            $restday = $restdays->is_restday;
        } else {
            $isWeekday = $formController->isWeekend($date);
            $restday = !$isWeekday ? 1 : 0;
        }
        $overtime = EmployeeOvertime::where('employee_id', $empId)
                        ->where('date', $date)
                        ->where('form_status_id', 3)->get()->first();
        $ot = array();
        if(!empty($overtime)) {
            $otEnd = $actualOut;
            // for night differential overtime
            $ndStart = $date . " 22:00:00";
            $ndEnd = date("Y-m-d" , date(strtotime("+1 day", strtotime($date)))) . " 06:00:00";

            $otStart = $overtime['datetime_from'];

            if ( (strtotime($overtime['datetime_to']) <= strtotime($ndStart) || (strtotime($overtime['datetime_from']) >= strtotime($ndEnd)) ) && !$holiday && !$restday) { // preshift ot and post shift
                $otOut = strtotime($startShift)  >= strtotime($overtime['datetime_to']) ? $overtime['datetime_to'] : $actualOut;
                $hrs = round((strtotime($otOut) - strtotime($otStart)) / (60*60), 2);
                if ( $hrs > 8 ) {
                    $ot['ot_hours'] = 8;
                    $ot['ot_excess'] = $hrs - $ot['ot_hours'];
                } else {
                    $ot['ot_hours'] = $hrs;
                    $ot['ot_excess'] = 0;
                }
            } else { // holiday and restday overtime
                if($holiday) {
                    if ($legal) {
                        $hol = 'leg';
                    } else {
                        $hol = 'spl';
                    }

                    if (strtotime($overtime['datetime_from']) < strtotime($ndStart) && strtotime($overtime['datetime_to']) < strtotime($ndEnd)) {
                        $hrs = round((strtotime($ndStart) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        if ($hrs > 8) {
                            $ot[$hol."ot"] = 8;
                            $ot[$hol."ot_excess"] = $hrs - $ot[$hol."ot"];
                        } else {
                            $ot[$hol."ot"] = $hrs;
                            $ot[$hol."ot_excess"] = 0;
                        }
                        $ot[$hol."_ndot"] = round((strtotime($overtime['datetime_to']) - strtotime($ndStart)) / (60*60), 2);
                    } elseif (strtotime($overtime['datetime_from']) > strtotime($ndStart) && strtotime($overtime['datetime_to']) < strtotime($ndEnd)) {
                        $ot[$hol."_ndot"] = round((strtotime($overtime['datetime_to']) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                    } elseif (strtotime($overtime['datetime_from']) > strtotime($ndStart) && strtotime($overtime['datetime_to']) > strtotime($ndEnd)) {
                        $ot[$hol."_ndot"] = round((strtotime($ndEnd) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        $hrs = round((strtotime($overtime['datetime_to']) - strtotime($ndEnd)) / (60*60), 2); // for clarification
                        if ($hrs > 8) {
                            $ot[$hol."ot"] = 8;
                            $ot[$hol."ot_excess"] = $hrs - $ot[$hol."ot"];
                        } else {
                            $ot[$hol."ot"] = $hrs;
                            $ot[$hol."ot_excess"] = 0;
                        }
                    } elseif (strtotime($overtime['datetime_from']) < strtotime($ndStart) && strtotime($overtime['datetime_to']) > strtotime($ndEnd)) {
                        $hrs = round((strtotime($ndStart) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        $ot[$hol."_ndot"] = round((strtotime($ndEnd) - strtotime($ndStart)) / (60*60), 2);
                        if ($hrs > 8) {
                            $ot[$hol."ot"] = 8;
                            $ot[$hol."ot_excess"] = $hrs - $ot[$hol."ot"];
                        } else {
                            $ot[$hol."ot"] = $hrs;
                            $ot[$hol."ot_excess"] = 0;
                        }
                        $ot[$hol."ot_excess"] = round((strtotime($overtime['datetime_to']) - strtotime($ndEnd)) / (60*60), 2); // for clarification if excess or tag as ot;
                    }
                } else { //restday
                    if (strtotime($overtime['datetime_from']) < strtotime($ndStart) && strtotime($overtime['datetime_to']) < strtotime($ndEnd)) {
                        $hrs = round((strtotime($ndStart) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        if ($hrs > 8) {
                            $ot['ot_hours'] = 8;
                            $ot['ot_excess'] = $hrs - $ot['ot_hours'];
                        } else {
                            $ot['ot_hours'] = $hrs;
                            $ot['ot_excess'] = 0;
                        }
                        $ot['ndot'] = round((strtotime($overtime['datetime_to']) - strtotime($ndStart)) / (60*60), 2);
                    } elseif (strtotime($overtime['datetime_from']) > strtotime($ndStart) && strtotime($overtime['datetime_to']) < strtotime($ndEnd)) {
                        $ot['ndot'] = round((strtotime($overtime['datetime_to']) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                    } elseif (strtotime($overtime['datetime_from']) > strtotime($ndStart) && strtotime($overtime['datetime_to']) > strtotime($ndEnd)) {
                        $ot['ndot'] = round((strtotime($ndEnd) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        $hrs = round((strtotime($overtime['datetime_to']) - strtotime($ndEnd)) / (60*60), 2); // for clarification
                        if ($hrs > 8) {
                            $ot['ot_hours'] = 8;
                            $ot['ot_excess'] = $hrs - $ot['ot_hours'];
                        } else {
                            $ot['ot_hours'] = $hrs;
                            $ot['ot_excess'] = 0;
                        }
                    } elseif (strtotime($overtime['datetime_from']) < strtotime($ndStart) && strtotime($overtime['datetime_to']) > strtotime($ndEnd)) {
                        $hrs = round((strtotime($ndStart) - strtotime($overtime['datetime_from'])) / (60*60), 2);
                        $ot['ndot'] = round((strtotime($ndEnd) - strtotime($ndStart)) / (60*60), 2);
                        if ($hrs > 8) {
                            $ot['ot_hours'] = 8;
                            $ot['ot_excess'] = $hrs - $ot['ot_hours'];
                        } else {
                            $ot['ot_hours'] = $hrs;
                            $ot['ot_excess'] = 0;
                        }
                        $ot['ot_excess'] = round((strtotime($overtime['datetime_to']) - strtotime($ndEnd)) / (60*60), 2); // for clarification if excess or tag as ot;
                    }
                }
            }
            return $ot;
        } else {
            return false;
        }
    }

    /* WORK HOURS COMPUTATION */
    public function getWorkHrs($actualIn, $actualOut)
    {
        $workHrs = (strtotime($actualOut) - strtotime($actualIn))/(60*60);
        return $workHrs > 0 ? round($workHrs, 2) : false;
    }

    /* LATE AND UNDERTIME COMPUTATION */
    public function getTardinessHrs($start, $end)
    {
        return !empty($start) && !empty($end) ? round((strtotime($end) - strtotime($start))/(60*60),2) : 0;
    }

    /* GET LEAVE APPROVED FORMS */
    public function getLeaves($empId, $date)
    {
        $leave = DB::table('employee_leaves AS el')
            ->leftJoin('employee_leave_dates AS eld', 'eld.employee_leave_id', '=', 'el.id')
            ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
            ->where('el.employee_id', $empId)
            ->where('eld.date', $date)
            ->where('el.form_status_id', 3)
            ->select('eld.date', 'ft.code', 'ft.form', 'eld.leave_credit', 'el.is_halfday', 'el.halfday_type')->get()->first();

        return $leave;
    }

    /* NDIFF COMPUTAION*/
    public function getNdiff($date, $actualIn, $actualOut)
    {    
        $ndiff = CompanyPolicy::find(1);//fixed company setup
        // echo "<pre>";print_r($ndiff->ndiff_start);
        $ndiffStart = $date . " " .$ndiff->ndiff_start;
        $ndiffEnd =  date("Y-m-d" , date(strtotime("+1 day", strtotime($date)))) . " " .$ndiff->ndiff_end;
        
        $startND = strtotime($actualIn) > strtotime($ndiffStart) ? $actualIn : $ndiffStart;
        $endND = strtotime($actualOut) < strtotime($ndiffEnd) ? $actualOut : $ndiffEnd;

        $nd = $this->getWorkHrs($startND, $endND);
        return $nd;
    }
}
