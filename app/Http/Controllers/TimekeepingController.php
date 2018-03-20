<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        
        foreach ($employees as $employee) {
            $data = array();
            $empId = $employee->employee_id;
            foreach ($periodCover as $workdate) {
                $day_type = '';
                $leave_type = '';
                $actualIn = $actualOut = 0;
                $absent = 0;
                $late = 0;
                $undertime = 0;
                $leave = 0;
                $work_hrs = 0;
                $ot_hrs = 0;
                $ot_excess = 0;
                $ndiff = 0;
                $ndot_excess = 0;
                $holiday = 0;
                
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
                        $day_type = 'sperd';
                    } elseif ($holidays->legal_holiday) {
                        $day_type = 'leg';
                    } else {
                        $day_type = 'spe';
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

                $dtOut = strtotime($shiftIn) > strtotime($shiftOut) ? date("Y-m-d" , date(strtotime("+1 day", strtotime($workdate)))) : $workdate;
                $endShift = $oiTime = $dtOut . " " .$shiftOut;
                $startShift = $ooTime = $workdate . " " . $shiftIn;
                echo "<pre>";print_r($empId."==".$workdate . " = " . $shiftIn."-".$shiftOut." == ".$dtOut);

                /* check if there is filed leave */
                $leaves = $this->getLeaves($empId, $workdate);

                $rawLogs = $this->getRawLogs($empId, $workdate, $dtOut, $leaves);
                // echo "<pre>";print_r($workdate);
                if (is_array($rawLogs) && count($rawLogs) > 0) {
                    // echo "<pre>";print_r($rawLogs);
                    foreach ($rawLogs as $log) {
                        $cType = $log['checktype'];
                        $cTime = $log['date']. " " . $log['checktime'];
                        if (strtotime($shiftIn) > strtotime($shiftOut)) { // NIGHT SHIFT EMPLOYEE
                            //time in
                            if ($cType == 'time_in') {
                                $actualIn = strtotime($cTime) < strtotime($oiTime) ? strtotime($cTime) > strtotime($workdate . " " . $shiftOut ) ? date("Y-m-d H:i", strtotime($cTime)) : 0 : date("Y-m-d H:i", strtotime($oiTime));
                                $oiTime = $actualIn;
                            }
                            //time out
                            if ($cType == 'time_out') {
                                $actualOut = strtotime($cTime) > strtotime($ooTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($ooTime));
                                $ooTime = $actualOut;
                            }
                        } else { // DAY SHIFT EMPLOYEE
                        }
                    }
                    // night differential for night shifts
                    $ndiff = strtotime($shiftIn) > strtotime($shiftOut) ? $this->getNdiff($workdate, $actualIn, $actualOut) : 0;
                } else { //no logs from dtr
                    /* check if date is restday */
                    if ($isWeekday) {
                        $absent = 1;
                    }
                }

                // if no computed in and out
                if (empty($actualIn) && empty($actualOut)) {
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
                }

                if(!$absent) {
                    if(!$holiday || $isWeekday) { // compute lates only if not holiday and not restday
                        /* GET LATES HOURS */
                        if (strtotime($startShift) < strtotime($actualIn)) {
                           $late = $this->getTardinessHrs($startShift, $actualIn, $leaves, $shiftId);
                        }

                        /* GET UNDERTIME HOURS */
                        if (strtotime($endShift) > strtotime($actualOut)) {
                            $undertime = $this->getTardinessHrs($actualOut, $endShift, $leaves, $shiftId);
                        }
                    }    

                    /* GET HOURS WORKED */
                    $work_hrs = $this->getWorkHrs($actualIn, $actualOut);

                    /*GET OT HOURS FROM FILED FORMS*/
                    $ot_hrs = $this->getOtHrs($empId, $workdate, $actualOut, $endShift);
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
                    'ot_hrs'        =>  $ot_hrs,
                    'ndiff'         =>  $ndiff,
                    'leave'         =>  $leave,
                    'leave_type'    =>  $leave_type
                ];

                echo "<pre>";print_r($data);

                // $data[$workdate]['period_id'] = $periodId;
                // $data[$workdate]['employee_id'] = $empId;
                // $data[$workdate]['date'] = $workdate; 
                // $data[$workdate]['shift_id'] = $shiftId;
                // $startShift = $workdate." ".$start;
                // $actualIn = $actualOut = '';
                // $absent = 0;
                
                // /* CHECK LEAVE FORMS FILED */
                // $leave = $this->getLeaves($empId, $workdate);
                // if (!empty($leave)) {
                //     $data[$workdate]['leave'] = true;
                //     $data[$workdate]['leave_type'] = $leave->form;
                // } else {
                //     if (strtotime($start) < strtotime($end)) { // DAY SHIFT EMPLOYEES
                //         $endShift = $oTime = $workdate . " " .$end;
                //         /* GET TIME IN/OUT FROM RAW LOGS */
                //         $rawLogs = RawLogs::where('date', $workdate)
                //             ->where('employee_id', $empId)->get()->toArray();
                //         if (is_array($rawLogs) && count($rawLogs) > 0) { 
                //             foreach ($rawLogs as $log) {
                //                 $cType = $log['checktype'];
                //                 $cTime = $log['date'] . " " . $log['checktime'];
                //                 /* GET THE EARLIEST TIME IN AND LATEST TIME OUT*/
                //                 if($cType == 'time_in') {
                //                     $actualIn = strtotime($cTime) < strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
                //                     $oTime = $data[$workdate][$cType] = $actualIn;
                //                 } else {
                //                     $actualOut = strtotime($cTime) > strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
                //                     $oTime = $data[$workdate][$cType] = $actualOut;
                //                 }
                //             }
                //         } else { // NO CAPTURED RAW LOGS
                //             $absent = $data[$workdate]['absent'] = true;
                //         }
                //     } else { // NIGHT SHIFT EMPLOYEES
                //         $dtOut = date("Y-m-d" , date(strtotime("+1 day", strtotime($workdate))));
                //         $endShift = $oTime = $dtOut . " " .$end;
                //         /* GET TIME IN FROM RAW LOGS */
                //         $rawIn = RawLogs::where('date', $workdate)
                //             ->where('employee_id', $empId)
                //             ->where('checktype', 'time_in')
                //             ->get()->toArray();
                //         /* GET TIME IN FROM RAW LOGS */
                //         foreach ($rawIn as $login) {
                //             /* GET THE EARLIEST TIME IN*/
                //             $cType = $login['checktype'];
                //             $cTime = $login['date'] . " " . $login['checktime'];
                //             $actualIn = strtotime($cTime) < strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
                //             $oTime = $data[$workdate][$cType] = $actualIn;
                //         }
                //         /* GET TIME OUT FROM RAW LOGS */
                //         $rawOut = RawLogs::where('date', $dtOut)
                //             ->where('employee_id', $empId)
                //             ->where('checktype', 'time_out')
                //             ->get()->toArray();
                        
                //         foreach ($rawOut as $logout) {
                //             /* GET THE LATEST TIME OUT*/
                //             $cType = $logout['checktype'];
                //             $cTime = $logout['date'] . " " . $logout['checktime'];
                //             $actualOut = strtotime($cTime) > strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
                //             $oTime = $data[$workdate][$cType] = $actualOut;
                //         }
                //     }
                //     $data[$workdate]['leave'] = false;
                //     $data[$workdate]['leave_type'] = "";
                //     if ($actualIn || $actualOut) {
                //         $absent = $data[$workdate]['absent'] = true;
                //     }
                // }

                // if (!$absent && !$leave) {
                // /* GET LATES HOURS */
                //     if (strtotime($startShift) < strtotime($actualIn)) {
                //        $data[$workdate]['late'] = $this->getTardinessHrs($startShift, $actualIn);
                //     }

                //     /* GET UNDERTIME HOURS */
                //     if (strtotime($endShift) > strtotime($actualOut)) {
                //         $data[$workdate]['undertime'] = $this->getTardinessHrs($actualOut, $endShift);
                //     }

                //     /* GET HOURS WORKED */
                //     $data[$workdate]['hours_work'] = $this->getWorkHrs($actualIn, $actualOut);

                //     /*GET OT HOURS FROM FILED FORMS*/
                //     $data[$workdate]['ot_hours'] = $this->getOtHrs($empId, $workdate, $actualOut, $endShift);
                // }

            } //end period days loop 
            


            /* SAVE DTR SUMMARY */
            // foreach ($data as $dtr) {
            //     /* CHECK IF RECORDS EXISTS (REPROCESSING) */
            //     $dtrExists = DB::table('tk_employee_dtr_summary')
            //                 ->where('period_id', $periodId)
            //                 ->where('employee_id', $empId)
            //                 ->where('date', $dtr['date'])
            //                 ->select('*')->get()->first();
                
            //     if (!empty($dtrExists)) {
            //         //update
            //         EmployeeDtrSummary::where('id', $dtrExists->id)
            //                     ->update($dtr);
            //     } else {
            //         //add new record
            //         EmployeeDtrSummary::create($dtr);
            //     }
            // }
        }die("ere");
    }

    /* GET SHIFT FROM EMPLOYEE WORKSCHEDULE */
    public function getShiftSchedule($empId, $date)
    {
        $shift = DB::table('employee_workschedule AS ew')
            ->leftJoin('shift AS s', 'ew.shift_id', '=', 's.id')
            ->where('ew.employee_id', $empId)
            ->where('ew.date_from', '<=', $date)
            ->where('ew.date_to', '>=', $date)
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
    public function getRawLogs($empId, $date, $dtOut, $leaves)
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
            $qry->where('date', $date)
                ->select('*');
            $rawLogs = $qry->get()->toArray();
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
    public function getOtHrs($empId, $date, $actualOut, $endShift)
    {
        $overtime = EmployeeOvertime::where('employee_id', $empId)
                        ->where('date', $date)
                        ->where('form_status_id', 3)->get()->first();
        $otEnd = strtotime($overtime['datetime_to']) <= strtotime($actualOut) ? $overtime['datetime_to'] : $actualOut;
        if (strtotime($endShift) < strtotime($otEnd)) {
            $otHrs = (strtotime($otEnd) - strtotime($overtime['datetime_from']))/(60*60);
            return round($otHrs, 2);
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
    public function getTardinessHrs($start, $end, $leaves, $shiftId)
    {
        if(!empty($leaves)) {
            $halfday = $leaves->is_halfday;
            $halfday_type = $leaves->halfday_type;
            $shift = Shift::find($shiftId);
            if ($halfday) {
                if($halfday == 1){
                    $halfEnd = date('Y-m-d', strtotime($end)). " " . $shift->second_halfday_start;
                    $end = strtotime($halfEnd) > strtotime($end) ? $end : $halfEnd;
                } else {
                    /*$halfStart = */$start = date('Y-m-d', strtotime($start)). " " . $shift->first_halfday_end;
                    // $start = ;
                }
                // echo "<pre>";print_r("IN :".$start. " = END: ". $end);
                return !empty($start) && !empty($end) ? round((strtotime($end) - strtotime($start))/(60*60),2) : 0;
            } else {
                return false;
            }
        } else {
            return !empty($start) && !empty($end) ? round((strtotime($end) - strtotime($start))/(60*60),2) : 0;
        }
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
