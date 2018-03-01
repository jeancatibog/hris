<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\RawLogs;

class TimekeepingController extends Controller
{
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
}
