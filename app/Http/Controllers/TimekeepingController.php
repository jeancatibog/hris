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
        		'message'	=>	"You have succesfuly Time " . $request['ctype'] ." at ". date('h:i A', strtotime($request['checktime'])) ]);

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
                        ->select('e.id AS employee_id', 'es.shift_id', 's.start', 's.end')->get(); /* GET ALL ACTIVE EMPLOYEES */
                        
        /*GET ALL RAW LOGS*/
        $rawLogs = RawLogs::whereBetween('date', [$period['start_date'], $period['end_date']])->get();
        $logs = array();

        foreach($employees as $employee) {
           foreach ($periodCover as $date) {
                $rawLogs = RawLogs::where('date', $date)
                        ->where('employee_id', $employee[''])->get();
            }
        }
        // foreach ($periodCover as $date) {
        //     foreach ($rawLogs as $raw) {
        //         /* GET EMPLOYEE SHIFT*/
        //         $shift = DB::table('employee_setup AS es')
        //                 ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
        //                 ->where('es.employee_id', $raw['employee_id'])
        //                 ->select('es.shift_id', 's.start', 's.end')->get()->first();

        //         /* CHECK IF EMPLOYEE HAS LEAVE */
        //     }
        // }
        die("jere");
        foreach ($rawLogs as $raw) {
            // DB::enableQueryLog();
            /* GET EMPLOYEE SHIFT*/
            $shift = DB::table('employee_setup AS es')
                        ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
                        ->where('es.employee_id', $raw['employee_id'])
                        ->select('es.shift_id', 's.start', 's.end')->get()->first();
            // dd($shift['end']);
            $in = '';
            $out = '';
            if($raw['checktype'] == 'In') {
                $in = $raw['checktime'];
            } else {
                $out = $raw['checktime'];
            }

            /*GET LEAVES FROM FILED FORMS*/
            // $onLeave = $this->getLeaveDates('')

            $data = [
                'employee_id'   =>  $raw['employee_id'],
                'date'          =>  $raw['date'],
                'shift_id'      =>  $shift->shift_id,
                'time_id'       =>  $in,
                'time_out'      =>  $out
            ];

            /*GET OT HOURS FROM FILED FORMS*/
            $otHrs = $this->getOtHrs($raw['employee_id'], $raw['date']);

            $data = array_prepend($data, $otHrs, 'ot_hours');
        }
        echo "<pre>";print_r($data);die("here");
    }

    /* OVERTIME COMPUTATION FROM FILED FORMS */
    public function getOtHrs($empId, $date)
    {
        $overtime = EmployeeOvertime::where('employee_id', $empId)
                        ->where('date', $date)
                        ->where('form_status_id', 3)->get()->first();
        
        $otHrs = (strtotime($overtime['datetime_to']) - strtotime($overtime['datetime_from']))/(60*60);
        return $otHrs;
    }
}
