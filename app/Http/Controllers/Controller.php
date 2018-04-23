<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;
use App\Employee;
use App\EmployeeSetup;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() 
    {
        $this->middleware(function ($request, $next) {
            view()->share('user', Employee::find(Auth::user()->employee_id));
            view()->share('setup', EmployeeSetup::where('employee_id', Auth::user()->employee_id)->get()->first());

            return $next($request);
        });
    }
}
