<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\EmployeeDtrSummary;
use App\TimekeepingPeriod;

class DtrController extends Controller
{
    /*
    * List view of employee daily time record
    */
    public function index()
    {
    	$first = date("Y-m-d", strtotime("first day of this month"));
		$last = date("Y-m-d", strtotime("last day of this month"));
    	$periods = TimekeepingPeriod::all();
    	$employeeDtr = DB::table('employees AS e')
    			->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
    			->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
    			->leftJoin('tk_employee_dtr_summary AS dtr', 'dtr.employee_id', '=', 'e.id')
                ->leftJoin('holiday AS h', 'dtr.date', '=', 'h.date_set')
    			->where('e.id', Auth::user()->employee_id)
    			->whereBetween('date',[$first, $last])
    			->select('dtr.*', 'e.employee_number', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS fullname'), 's.start', 's.end', 'h.holiday')
    			->get()->toArray();
    	// echo "<pre>";print_r($employeeDtr);die("here");
        return view('dtr/index', ['employeeDtr' => $employeeDtr]);
    }
}
