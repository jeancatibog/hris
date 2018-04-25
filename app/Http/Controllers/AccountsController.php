<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DatePeriod;
use DateInterval;
use App\AccountsTeamLead;
use App\Employee;
use App\Account;
use App\Shift;

class AccountsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "destroy"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = DB::table('accounts_team_lead AS tl')
        		->leftJoin('account AS a', 'tl.account_id', '=', 'a.id')
        		->leftJoin('employees AS e', 'tl.team_lead_id', '=', 'e.id')
                ->leftJoin('shift AS s', 'tl.shift_id', '=', 's.id')
        		->select('tl.id', 's.name AS shift', 'tl.date_from', 'tl.date_to', 'a.name','tl.team_lead_id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS team_lead'))
        		->get()->toArray();
        return view('system-mgmt/accounts/index', ['accounts' => $accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	$accounts = Account::all();
    	$employees = Employee::all();
        $shifts = Shift::all()->sortBy("start");
        $days = DB::table('day')->get();
        return view('system-mgmt/accounts/create', ['accounts' => $accounts, 'employees' => $employees, 'shifts' => $shifts, 'days' => $days]);
    }

    public function loadTeamLead($accountId)
    {
    	$leads = DB::table('employees AS e')
    		->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
    		->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
    		->where('es.account_id', $accountId)
    		->where('r.name', 'Team Lead')
    		->select('e.id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'))->get();
        return response()->json($leads);
    }

    public function loadAgents($accountId)
    {
        // check if agents already has manage
        $manage = DB::table('accounts_team_lead AS tl')
            ->where('tl.account_id', $accountId)->get();
        $agentIds = [];
        foreach ($manage as $agent) {
            array_push($agentIds, $agent->agent_ids);
        }

        $agents = DB::table('employees AS e')
            ->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->where('es.account_id', $accountId)
            ->whereNotIn('r.name', ['Team Lead', 'Supervisor', ])
            ->whereNotIn('e.id', [implode($agentIds, ",")])
            ->select('e.id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'))->get();
        return response()->json($agents);
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
        $agents = $request['agent_ids'];
        $rd = $request['restdays'];
        $acctLead = AccountsTeamLead::create([
            'account_id'  =>    $request['account_id'],
            'shift_id'    =>    $request['shift_id'],
            'date_from'		=> 	date('Y-m-d', strtotime($request['date_from'])),
	        'date_to'		=> 	date('Y-m-d', strtotime($request['date_to'])),
            'restday_ids'  =>   implode($request['restdays'], ","),
            'team_lead_id' =>   $request['team_lead_id'],
            'agent_ids'    =>   implode($request['agent_ids'], ",")
        ]);
        $id = $acctLead->id; 
        $this->setWorkshift($request['date_from'], $request['date_to'], $request['shift_id'], $agents, $rd, $id);

        return redirect()->intended('system-management/accounts');
    }

    public function setWorkshift($from, $to, $shift, $agents, $rd, $id)
    {
        DB::table('employee_workschedule')->where('account_tl_id', $id)->delete();;
        foreach ($agents as $key => $agentId) {
            $start = new DateTime($from);
            $end = new DateTime($to);
            $oneDay = new DateInterval('P1D');
            $period = new DatePeriod(
                    $start,
                    $oneDay,
                    $end->add($oneDay)
            );
            $all_days = array();$i = 0;
            foreach($period as $date) {
                $date = $date->format('Y-m-d');
                if (in_array($this->getDayId($date), $rd)){
                    $rdShift = DB::table('shift')->where('is_restday', 1)->select('id')->first();
                    $shiftId = $rdShift->id;
                }else {
                    $shiftId = $shift;
                }

                $input = [
                    'employee_id'   =>  $agentId,
                    'date'          =>  $date,
                    'shift_id'      =>  $shiftId,
                    'account_tl_id' => $id
                ];
                DB::table('employee_workschedule')->insert($input);
            }
        }
    }

    public function getDayId($date) 
    {   
        $day = date('w', strtotime($date));
        return $day;
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
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $accTL = AccountsTeamLead::find($id);
        $accounts = Account::all();
        $shift = Shift::all();
        $days = DB::table('day')->get();
    	$leads = DB::table('employees AS e')
    		->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
    		->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
    		->where('r.name', 'Team Lead')
            ->where('es.account_id', $accTL->account_id)
    		->select('e.id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'))->get();
        $agents = DB::table('employees AS e')
            ->leftJoin('employee_setup AS es', 'es.employee_id', '=', 'e.id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->whereIn('r.name', ['Senior Associate', 'Associate'])
            ->where('es.account_id', $accTL->account_id)
            ->select('e.id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS name'))->get()->toArray();
        return view('system-mgmt/accounts/edit', ['acctTL' => $accTL, 'accounts' => $accounts, 'teamleads' => $leads, 'agents' => $agents, 'shifts' => $shift,'days' => $days]);
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
        $this->validateInput($request, $id);
        $agents = $request['agent_ids'];
        $rd = $request['restdays'];
        $input = [
            'account_id'  =>  $request['account_id'],
            'shift_id'    =>    $request['shift_id'],
            'date_from'		=> 	date('Y-m-d', strtotime($request['date_from'])),
	        'date_to'		=> 	date('Y-m-d', strtotime($request['date_to'])),
            'restday_ids'  =>   implode($request['restdays'], ","),
            'team_lead_id' =>   $request['team_lead_id'],
            'agent_ids'    =>   implode($request['agent_ids'], ",")
        ];
        AccountsTeamLead::where('id', $id)
            ->update($input);
        
        $this->setWorkshift($request['date_from'], $request['date_to'], $request['shift_id'], $agents, $rd, $id);
        return redirect()->intended('system-management/accounts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AccountsTeamLead::where('id', $id)->delete();
         return redirect()->intended('system-management/accounts');
    }

    /**
     * Search shift from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $constraints = [
            'name' => $request['accountname']
            ];

       $accounts = $this->doSearchingQuery($constraints);
       return view('system-mgmt/accounts/index', ['accounts' => $accounts, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        // $query = AccountsTeamLead::query();
        $query = DB::table('accounts_team_lead AS tl')
        		->leftJoin('account AS a', 'tl.account_id', '=', 'a.id')
        		->leftJoin('employees AS e', 'tl.team_lead_id', '=', 'e.id')
        		->select('tl.id','a.name', 'tl.date_from', 'tl.date_to', 'tl.team_lead_id', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS team_lead'));
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(5);
    }

    private function validateInput($request, $id=NULL)
    {
    	$from = $request['date_from'];
    	$to = $request['date_to'];
        $message = [
			'overlap' => 'Schedule for the account team lead overlaps another schedule'
		];
        // check if accounts has overlap schedule
        $schedule = AccountsTeamLead::where('account_id', $request['account_id'])
        		->where(function ($query) use ($from, $to) {
        			$query->where('date_from', '>=', $from)->where('date_from', '<=', $to)
		              ->orWhere('date_from', '<=', $from)->where('date_to', '>=', $to)
		              ->orWhere('date_to', '>', $from)->where('date_to', '<=', $to)
		              ->orWhere('date_from', '>=', $from)->where('date_to', '<=', $to);
        		});
		if ($id) {
        	$schedule->where('id', '!=', $id);
        }
        $sched = $schedule->get()->toArray();

        if (count($sched) > 0) {
        	$overlaps = true;
        } else {
        	$overlaps = false;
        }
        $this->validate($request, [
            'account_id'	=>  'required',
            'shift_id'      =>  'required',
            'date_from'		=>	'required|date',
	        'date_to'		=>	'required|date|after_or_equal:date_from|'. ($overlaps ? 'overlap' : ''),
            'team_lead_id'	=>  'required'
        ], $message);
    }
}
