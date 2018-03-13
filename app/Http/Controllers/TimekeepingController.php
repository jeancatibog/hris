<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\RawLogs;
use App\TimkeepingPeriod;

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
    public function period_cover()
    {
        $periods = TimkeepingPeriod::all();
        return view('timekeeping/period/index', ['periods' => $periods]);
    }


    public function create_period()
    {
        return view('timekeeping/period/create');
    }
}
