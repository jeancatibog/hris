<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\RawLogs;
use App\TimekeepingPeriod;
use App\EmployeeOvertime;
use App\Employee;
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
        $periodCover = $formController->getDatesFromRange($period['start_date'], $period['end_date']);
        $employees = DB::table('employees AS e')
                        ->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
                        ->leftJoin('employment_status AS est', 'es.status_id', '=', 'est.id')
                        ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
                        ->where('est.is_active', 1)
                        ->select('e.id AS employee_id', 'es.shift_id', 's.start', 's.end')->get()->toArray(); /* GET ALL ACTIVE EMPLOYEES */
        
        /*GET ALL RAW LOGS*/
        // $rawLogs = RawLogs::whereBetween('date', [$period['start_date'], $period['end_date']])->get();
        $data = array();

        foreach ($employees as $employee) {
            $empId = $employee->employee_id;
            $start = $employee->start;
            $end = $employee->end;
            foreach ($periodCover as $workdate) {
                echo "<pre>";print_r($workdate);
                if (strtotime($start) < strtotime($end)) { // DAY SHIFT EMPLOYEES
                    $oTime = $workdate . " " .$employee->end;

                    $rawLogs = RawLogs::where('date', $workdate)
                        ->where('employee_id', $empId)->get()->toArray();
                } else { // NIGHT SHIFT EMPLOYEES
                    $dtOut = date("Y-m-d" , date(strtotime("+1 day", strtotime($workdate))));
                    $oTime = $dtOut . " " .$employee->end;
                    
                    $rawLogs = RawLogs::whereIn('date', [$workdate, $dtOut])
                        ->where('employee_id', $empId)->get()->toArray();
                    echo "<pre>";print_r($rawLogs);
                }
            }  die("ere");

            /* 1st Logic */
            // foreach ($periodCover as $date) {
            //     $rawLogs = RawLogs::where('date', $date)
            //             ->where('employee_id', $empId)->get()->toArray();
            //     $oTime = $date . " " .$employee->end;
            //     if (is_array($rawLogs) && count($rawLogs) > 0) {
            //         foreach ($rawLogs as $log) {
            //             $cType = $log['checktype'];
            //             $cTime = $log['date'] . " " . $log['checktime'];
            //             if (strtotime($start) < strtotime($end)) { // DAY SHIFT EMPLOYEES
            //                 $startShift = $date." ".$start;
            //                 $endShift = $date." ".$end;
            //                 /* GET THE EARLIEST TIME IN AND LATEST TIME OUT*/
            //                 if($cType == 'time_in') {
            //                     $in = strtotime($cTime) < strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
            //                     $data[$cType] = $in;
            //                 } else {
            //                     $out = strtotime($cTime) > strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
            //                     $data[$cType] = $out;
            //                 }
            //                 $oTime = $data[$cType];
            //             } else { // NIGHT SHIFT EMPLOYEES
            //                 $startShift = $date." ".$start;
            //                 $endShift = date("Y-m-d" , date(strtotime("+1 day", strtotime($date))))." ".$end;
            //                 /* GET THE EARLIEST TIME IN AND LATEST TIME OUT*/
            //                 if($cType == 'time_in') {
            //                     $in = strtotime($cTime) < strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
            //                     $data[$cType] = $in;
            //                 } else {
            //                      $out = '06:00';//strtotime($cTime) > strtotime($oTime) ? date("Y-m-d H:i", strtotime($cTime)) : date("Y-m-d H:i", strtotime($oTime));
            //                     $data[$cType] = $out;
            //                 }
            //             }
            //         }
            //         /* GET LATES HOURS */
            //         if(strtotime($startShift) < strtotime($in)) {
            //             $data['late'] = $this->getTardinessHrs($startShift, $in);
            //         }

            //         /* GET UNDERTIME HOURS */
            //         if(strtotime($endShift) > strtotime($out)) {
            //             $data['undertime'] = $this->getTardinessHrs($out, $endShift);
            //         }

            //         /* GET HOURS WORKED */
            //         $data['hours_work'] = $this->getWorkHrs($in, $out);

            //         /*GET OT HOURS FROM FILED FORMS*/
            //         $data['ot_hours'] = $this->getOtHrs($empId, $date, $out, $endShift);

            //     } else { // IF THERE IS NO RAW LOGS
            //         /* CHECK IF HOLIDAY */

            //         /* CHECK IF THERE ARE LEAVES FILED */

            //         /* CHECK IF THERE IS OBT FILED */
            //     }
            // }
        }echo "<pre>";print_r($data);die("testing");
        
        echo "<pre>";print_r($data);die("here");
    }

    /* OVERTIME COMPUTATION FROM FILED FORMS */
    public function getOtHrs($empId, $date, $out, $endShift)
    {
        $overtime = EmployeeOvertime::where('employee_id', $empId)
                        ->where('date', $date)
                        ->where('form_status_id', 3)->get()->first();
        $otEnd = strtotime($overtime['datetime_to']) <= strtotime($out) ? $overtime['datetime_to'] : $out;
        if (strtotime($endShift) < $otEnd) {
            $otHrs = (strtotime($otEnd) - strtotime($overtime['datetime_from']))/(60*60);
            return round($otHrs, 2);
        } else {
            return false;
        }
        
    }

    /* WORK HOURS COMPUTATION */
    public function getWorkHrs($in, $out)
    {
        $workHrs = (strtotime($out) - strtotime($in))/(60*60) - 1;
        return round($workHrs, 2);
    }

    /* TARDINESS LATE/UNDERTIME COMPUTATION */
    public function getTardinessHrs($start, $end)
    {
        $tardy = (strtotime($end) - strtotime($start))/(60*60);
        return $tardy > 0 ? $tardy : false;
    }
}
