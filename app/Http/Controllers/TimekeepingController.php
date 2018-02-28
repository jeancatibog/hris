<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RawLogs;

class TimekeepingController extends Controller
{
    /*
	* Logs all in and  out of employee tru bundy clock on dashboard
    */
    public function log(Request $request)
    {
         RawLogs::create([
            'employee_id'	=> 	$request['employee_id'],
        ]);

        return redirect()->intended('dashboard.index');
    }
}
