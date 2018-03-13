<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\RawLogs;
use App\TimekeepingPeriod;

class TimekeepingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
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

    /*
    * Creation of timekeeping period coverage
    */
    public function period()
    {//DB::enableQueryLog();
        $periods = DB::table('tk_period AS tkp')
                ->leftJoin('tk_period_status AS tkps', 'tkp.status_id', '=', 'tkps.id')
                ->select('tkp.*', 'tkps.status')
                ->paginate(5);
        //dd(DB::getQueryLog());
        return view('timekeeping/period/index', ['periods' => $periods]);
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
    public function update(Request $request, $id)
    {
         /*$this->validate($request, [
        'name' => 'required|max:60'
        ]);*/
        $input = [
            'start_date' =>  date("Y-m-d",strtotime($request['start_date'])),
            'end_date'   =>  date("Y-m-d",strtotime($request['end_date']))
        ];
        Shift::where('id', $id)
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

    private function validateInput($request) {
        $this->validate($request, [
            'start_date' =>  'required',
            'end_date'   =>  'required|date|after_or_equal:start_date'
        ]);
    }
}
