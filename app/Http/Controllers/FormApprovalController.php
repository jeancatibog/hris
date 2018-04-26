<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use DateTime;
use DatePeriod;
use DateInterval;
use App\FormType;
use App\Employee;
use App\EmployeeLeaves;
use App\EmployeeLeaveDates;
use App\EmployeeOvertime;
use App\EmployeeObt;
use App\CompanyPolicy;
use App\EmployeeDtrp;
use App\Http\Controllers\TimekeepingController;

class FormApprovalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($search=NULL)
    {
        $employee_id = Auth::user()->employee_id;
        $employee_details = DB::table('employees AS e')
                ->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
                ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
                ->where('es.employee_id', $employee_id)
                ->select('es.team_id', 'r.name AS role','es.*')->get()->first();

        $acct_sched = DB::table('accounts_team_lead AS tl')
            ->leftJoin('account AS a', 'tl.account_id', '=', 'a.id')
            ->where('tl.account_id', $employee_details->account_id)
            ->where('tl.team_lead_id', $employee_details->employee_id)
            ->get()->toArray();

        // DB::enableQueryLog();
        $leave_form = DB::table('employee_leaves AS el')
            ->leftJoin('employees AS e', 'el.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'el.employee_id', '=', 'es.employee_id')
            ->leftJoin('form_status AS fs', 'el.form_status_id', '=', 'fs.id')
            ->leftJoin('form_type AS ft', 'el.form_type_id', '=', 'ft.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            /*->leftJoin('employee_workschedule AS ew', function($join)
            {
                $join->on('el.date_from', '=', 'ew.date');
                $join->on('el.employee_id','=','ew.employee_id');
            })*/
            ->where(function ($query) use ($acct_sched, $employee_details, $employee_id) {
                if (count($acct_sched) > 0) {
                    foreach ($acct_sched as $sched) {
                        $query->orWhereBetween('el.date_from',[$sched->date_from, $sched->date_to]);
                        $query->whereIn('el.employee_id',explode(",", $sched->agent_ids));
                        $query->where('es.account_id', $sched->account_id);
                    }

                } else {
                    $query->where('es.approver_id', $employee_id); // default approver
                }
            })
            ->where('fs.status', 'For Approval')
            ->where('es.team_id', $employee_details->team_id)
            ->where('el.employee_id', '!=', $employee_id); //exclude own filed forms
        if ($employee_details->role == 'Team Lead') {
            $leave_form->whereIn('r.name', ['Associate', 'Senior Associate']); // for lower role only
        } elseif ($employee_details->role == 'Supervisor') {
            $leave_form->whereIn('r.name', ['Associate', 'Senior Associate', 'Team Lead']); // will add Assos and Sr. Assoc if approver is absent
        } elseif ($employee_details->role == 'Manager') {
            $leave_form->whereIn('r.name', ['Assistant Manager', 'Supervisor', 'Team Lead', 'Associate', 'Senior Associate']); // will add TL and Assoc and Sr. Assoc if approver is absent
        }elseif ($employee_details->role == 'Director') {
            $leave_form->where('r.name', 'Manager');
        }elseif ($employee_details->role == 'President') {
            $leave_form->whereIn('r.name', ['Manager', 'Director']);
        }
        if (!is_null($search)) {
            $leave_form->where('r.name', $search);
        }
        $leave_form->orderBy('r.id', 'asc');
        $leave_form->select(DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'), 'el.id', 'ft.form', 'fs.status', 'el.date_from', 'el.date_to', 'el.reason', 'r.name as role');

        $leave = $leave_form->get()->toArray();
        // echo "<pre>";print_r($leave);die("here");
        // echo "<pre>";print_r($acct_sched);die("here");
        // echo "<pre>";print_r(DB::getQueryLog());die("jere");

        $ot_form = DB::table('employee_overtime AS ot')
            ->leftJoin('employees AS e', 'ot.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'ot.employee_id', '=', 'es.employee_id')
            ->leftJoin('form_status AS fs', 'ot.form_status_id', '=', 'fs.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            /*->leftJoin('employee_workschedule AS ew', function($join)
            {
                $join->on('ot.date', '=', 'ew.date');
                $join->on('ot.employee_id','=','ew.employee_id');
            })*/
            ->leftJoin('shift AS s', 'es.shift_id', '=', 's.id')
            ->where(function ($query) use ($acct_sched, $employee_details, $employee_id) {
                if (count($acct_sched) > 0) {
                    foreach ($acct_sched as $sched) {
                        $query->orWhereBetween('ot.date',[$sched->date_from, $sched->date_to]);
                        $query->whereIn('ot.employee_id',explode(",", $sched->agent_ids));
                        $query->where('es.account_id', $sched->account_id);
                    }
                } else {
                    $query->where('es.approver_id', $employee_id);
                }
            })
            ->where('fs.status', 'For Approval')
            ->where('es.team_id', $employee_details->team_id)
            ->where('ot.employee_id', '!=', $employee_id); //exclude own filed forms
        if ($employee_details->role == 'Team Lead') {
            $ot_form->whereIn('r.name', ['Associate', 'Senior Associate']); // for lower role only
        } elseif ($employee_details->role == 'Supervisor') {
            $ot_form->whereIn('r.name', ['Associate', 'Senior Associate', 'Team Lead']); // will add Assos and Sr. Assoc if approver is absent
        } elseif ($employee_details->role == 'Manager') {
            $ot_form->whereIn('r.name', ['Assistant Manager', 'Supervisor', 'Team Lead', 'Associate', 'Senior Associate']); // will add TL and Assoc and Sr. Assoc if approver is absent
        }elseif ($employee_details->role == 'Director') {
            $ot_form->where('r.name', 'Manager');
        }elseif ($employee_details->role == 'President') {
            $ot_form->whereIn('r.name', ['Manager', 'Director']);
        }
        $ot_form->orderBy('r.id', 'asc');
        $ot_form->select(DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'), 'ot.id', 'fs.status', 'ot.date', 'ot.datetime_from', 'ot.datetime_to', 'ot.reason', 's.is_restday', 'r.name as role');

        $ot = $ot_form->get()->toArray();

        $obt_form = DB::table('employee_obt AS obt')
            ->leftJoin('employees AS e', 'obt.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'obt.employee_id', '=', 'es.employee_id')
            ->leftJoin('form_status AS fs', 'obt.form_status_id', '=', 'fs.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            /*->leftJoin('employee_workschedule AS ew', function($join)
            {
                $join->on('obt.date_from', '=', 'ew.date');
                $join->on('obt.employee_id','=','ew.employee_id');
            })*/
            ->where(function ($query) use ($acct_sched, $employee_details, $employee_id) {
                if (count($acct_sched) > 0) {
                    foreach ($acct_sched as $sched) {
                        $query->orWhereBetween('obt.date_from',[$sched->date_from, $sched->date_to]);
                        $query->whereIn('obt.employee_id',explode(",", $sched->agent_ids));
                        $query->where('es.account_id', $sched->account_id);
                    }

                    $query->where('es.account_id', $employee_details->account_id);
                } else {
                    $query->where('es.approver_id', $employee_id);
                }
            })
            ->where('fs.status', 'For Approval')
            ->where('es.team_id', $employee_details->team_id)
            ->where('obt.employee_id', '!=', $employee_id); //exclude own filed forms
        if ($employee_details->role == 'Team Lead') {
            $obt_form->whereIn('r.name', ['Associate', 'Senior Associate']); // for lower role only
        } elseif ($employee_details->role == 'Supervisor') {
            $obt_form->whereIn('r.name', ['Associate', 'Senior Associate', 'Team Lead']); // will add Assos and Sr. Assoc if approver is absent
        } elseif ($employee_details->role == 'Manager') {
            $obt_form->whereIn('r.name', ['Assistant Manager', 'Supervisor', 'Team Lead', 'Associate', 'Senior Associate']); // will add TL and Assoc and Sr. Assoc if approver is absent
        }elseif ($employee_details->role == 'Director') {
            $obt_form->where('r.name', 'Manager');
        }elseif ($employee_details->role == 'President') {
            $obt_form->whereIn('r.name', ['Manager', 'Director']);
        }
        $obt_form->orderBy('r.id', 'asc');
        $obt_form->select(DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'), 'obt.id', 'fs.status', 'obt.date_from', 'obt.date_to', 'obt.starttime', 'obt.endtime', 'obt.reason', 'contact_name', 'contact_info', 'contact_position', 'company_to_visit', 'company_location', 'r.name as role');

        $obt = $obt_form->get()->toArray();

        $dtrp_form = DB::table('employee_dtrp AS dtrp')
            ->leftJoin('employees AS e', 'dtrp.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'dtrp.employee_id', '=', 'es.employee_id')
            ->leftJoin('form_status AS fs', 'dtrp.form_status_id', '=', 'fs.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            /*->leftJoin('employee_workschedule AS ew', function($join)
            {
                $join->on('dtrp.date', '=', 'ew.date');
                $join->on('dtrp.employee_id','=','ew.employee_id');
            })*/
            ->where(function ($query) use ($acct_sched, $employee_details, $employee_id) {
                if (count($acct_sched) > 0) {
                    foreach ($acct_sched as $sched) {
                        $query->orWhereBetween('dtrp.date',[$sched->date_from, $sched->date_to]);
                        $query->whereIn('dtrp.employee_id',explode(",", $sched->agent_ids));
                        $query->where('es.account_id', $sched->account_id);
                    }

                    $query->where('es.account_id', $employee_details->account_id);
                } else {
                    $query->where('es.approver_id', $employee_id);
                }
            })
            ->where('fs.status', 'For Approval')
            ->where('es.team_id', $employee_details->team_id)
            ->where('dtrp.employee_id', '!=', $employee_id); //exclude own filed forms
        if ($employee_details->role == 'Team Lead') {
            $dtrp_form->whereIn('r.name', ['Associate', 'Senior Associate']); // for lower role only
        } elseif ($employee_details->role == 'Supervisor') {
            $dtrp_form->whereIn('r.name', ['Associate', 'Senior Associate', 'Team Lead']); // will add Assos and Sr. Assoc if approver is absent
        } elseif ($employee_details->role == 'Manager') {
            $dtrp_form->whereIn('r.name', ['Assistant Manager', 'Supervisor', 'Team Lead', 'Associate', 'Senior Associate']); // will add TL and Assoc and Sr. Assoc if approver is absent
        }elseif ($employee_details->role == 'Director') {
            $dtrp_form->where('r.name', 'Manager');
        }elseif ($employee_details->role == 'President') {
            $dtrp_form->whereIn('r.name', ['Manager', 'Director']);
        }
        $dtrp_form->orderBy('r.id', 'asc');
        $dtrp_form->select(DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'), 'dtrp.id', 'fs.status', 'dtrp.date', 'dtrp.log_type_id', 'dtrp.timelog', 'dtrp.reason', 'r.name as role');

        $dtrp = $dtrp_form->get()->toArray();
        
        $approval_form['leaves'] = $leave;
        $approval_form['ot'] = $ot;
        $approval_form['obt'] = $obt;
        $approval_form['dtrp'] = $dtrp;
        return view('form-approval/index', ['form_approval' => $approval_form]);
    }


    /**
     * Store a newly created form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $returnHTML = "";
        $form = $_GET['form'];
        if($form == 'leave') {
            $leave = EmployeeLeaves::find($id);
            
            $employee_detail = Employee::find(Auth::user()->employee_id);
            
            $query = FormType::where('is_leave', 1);
            if($employee_detail['gender'] == 1) {
                $query->where('for_men', 1);
            } else {
                $query->where('for_women', 1);
            }
            $types = $query->get();
            $file_form['leave'] = $leave;
            $params = ['form' => $file_form[$form], 'types' => $types];
        } elseif($form == 'overtime') {
            $ot = EmployeeOvertime::find($id);

            $file_form['overtime'] = $ot;

            $params = ['form' => $file_form[$form]];
        } elseif($form == 'obt') {
            $obt = EmployeeObt::find($id);
            $obt['starttime'] = date('h:i A', strtotime($obt['starttime']));
            $obt['endtime'] = date('h:i A', strtotime($obt['endtime']));

            $file_form['obt'] = $obt;

            $params = ['form' => $file_form[$form]];
        } elseif($form == 'dtrp') {
            $dtrp = EmployeeDtrp::find($id);
            $dtrp['timelog'] = date('h:i A', strtotime($dtrp['timelog']));

            $file_form['dtrp'] = $dtrp;

            $params = ['form' => $file_form[$form]];
        }
        
        $params['for_approval'] = true;

        $returnHTML = view('forms.'.$form.'.edit')->with($params)->render();

        return response()->json( array('html'=>$returnHTML, 'form' => $form, 'id' => $id) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id=0)
    {
        $status = $request['action_id'];
        $input = [
            'approvers_remarks' =>  $request['approvers_remarks'],
            'date_approved'     =>  date('Y-m-d H:i:s'),// could also be the date disapproved or canlled
            'approved_by'       =>  Auth::user()->employee_id, // person or approver who also disapproves and cancels
            'form_status_id'    =>  $status
        ];
    	if ($request['ftype'] == 'leave') {
            $credits = DB::table('employee_leaves AS el')
                ->leftJoin('employee_leave_dates AS eld', 'eld.employee_leave_id', '=', 'el.id')
                ->where('el.id', $id)
                ->sum('eld.leave_credit');
            if ($status == 4 || $status == 5) {
                //return leave credit
            }
	        EmployeeLeaves::where('id', $id)
	            ->update($input);
	    } elseif ($request['ftype'] == 'overtime') {
	        EmployeeOvertime::where('id', $id)
	            ->update($input);
	    } elseif ($request['ftype'] == 'obt') {
            EmployeeObt::where('id', $id)
                ->update($input);
        } elseif ($request['ftype'] == 'dtrp') {
            EmployeeDtrp::where('id', $id)
                ->update($input);
        } 
        
        return redirect()->intended('form-approval');
    }

    public function search(Request $request) {
        // $constraints = [
        //     'name' => $request['name']
        //     ];
        $this->index($request['name']);
        // return view('form-approval/index', ['employees' => $employees, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        // ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('province', 'employees.province_id', '=', 'province.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        // ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->select('employees.firstname as employee_name', 'employees.*');
        //,'department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(5);
    }
}
