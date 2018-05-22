<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use Excel;
use Illuminate\Support\Facades\DB;
use Auth;
use PDF;
use Exporter;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        // $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }

    public function index() {
        date_default_timezone_set('asia/ho_chi_minh');
        $format = 'Y/m/d';
        $now = date($format);
        $to = date($format, strtotime("+30 days"));
        $constraints = [
            'from' => $now,
            'to' => $to
        ];

        $employees = $this->getHiredEmployees($constraints);
        return view('report/index', ['employees' => $employees, 'searchingVals' => $constraints]);
    }

    public function exportExcel(Request $request) {
        $this->prepareExportingData($request)->export('xls');
        redirect()->intended('report');
    }

    /*public function exportPDF(Request $request) {
         $constraints = [
            'from' => $request['from'],
            'to' => $request['to']
        ];
        $employees = $this->getExportingData($constraints);
        $pdf = PDF::loadView('system-mgmt/report/pdf', ['employees' => $employees, 'searchingVals' => $constraints]);
        return $pdf->download('report_from_'. $request['from'].'_to_'.$request['to'].'pdf');
        // return view('system-mgmt/report/pdf', ['employees' => $employees, 'searchingVals' => $constraints]);
    }*/
    
    private function prepareExportingData($request) {
        $author = Auth::user()->username;
        
        if ($request->report_id == 1) {
            $report = 'Attendance Report';
            $data = $this->getExportingAttendanceData(['from'=> $request->date_from, 'to' => $request->date_to]);
        } elseif ($request->report_id == 2) {
            $report = 'Overtime Report';
            $data = $this->getExportingOvertimeData(['from'=> $request->date_from, 'to' => $request->date_to]);
        } elseif ($request->report_id == 3) {
            $report = 'Tardiness Report';
            $data = $this->getExportingTardinessData(['from'=> $request->date_from, 'to' => $request->date_to]);
        } else {
            $report = 'Leave Report';
            $data = $this->getExportingLeaveData(['from'=> $request->date_from, 'to' => $request->date_to]);
        }

        

        return Excel::create($report . $request['date_from'].'_to_'.$request['date_to'], function($excel) use($data, $request, $author, $report) {

            // Set the title
            $excel->setTitle('List of hired employees from '. $request['from'].' to '. $request['to']);

            // Chain the setters
            $excel->setCreator($author)
                ->setCompany('Transcosmos Asia Philippines');

            // Call them separately
            $excel->setDescription('The list of hired employees');

            $excel->sheet($report, function($sheet) use($data, $request, $report) {
                /*$range = range("A", "Z");
                if ($request->report_id == 1) {
                    $headers = array('Date', 'Day', 'Classification', 'Ee number', 'Ee Name', 'Role Title', 'Shift', 'Team', 'Account', 'Time-in', 'Time-out', 'Actual Total ND (in Hours)', 'OT Hours' => array('Time-in', 'Time-out', 'Total Approved OT Hours'), 'Tardiness/Undertime' => array('in Hours'), 'Leave' => array('Type of Leave', 'No. of Leave Days'));
                    // echo "<pre>";print_r($headers);exit;
                    // custom header
                    $i = 0;
                    foreach ($headers as $key => $header) {
                        $start = $range[$i];
                        if( is_array($header) && count($header) > 0) {
                            foreach($header as $value) {
                                $sheet->cell($range[$i].'6', function($cell) use ($value) {
                                    $cell->setValue($value);
                                });
                                $i++;
                            }
                            $i = $i-1;
                            $end = $range[$i];
                            $merge = $start.'5'.':'.$end.'5';

                            $sheet->cells($start.'5', function ($cells) use ($key) {
                                $cells->setAlignment('center');//->setWrapText(true);

                                $cells->setValue($key);
                            });

                            $sheet->mergeCells($merge);
                        } else {
                            $sheet->cell($range[$i].'6', function($cell) use ($header) {
                                $cell->setValue($header);
                            });
                        }

                        $i++;
                    }
                        // die("here");
                }

                $sheet->fromArray($data);*/
                // echo "<pre>";print_r(strtolower(str_replace(" ", "_", $report)));die("here");
                $sheet->loadView('report/'.strtolower(str_replace(" ", "_", $report)), array('data' => $data, 'report'=> $report, 'range' => $request));
            });
        });
    }

    public function search(Request $request) {
        // $constraints = [
        //     'from' => $request['from'],
        //     'to' => $request['to']
        // ];

        // $employees = $this->getHiredEmployees($constraints);
        // return view('system-mgmt/report/index', ['employees' => $employees, 'searchingVals' => $constraints]);
    }

    private function getHiredEmployees($constraints) {
        $employees = DB::table('employees')
            ->leftJoin('employee_setup', 'employee_setup.employee_id', '=', 'employees.id')
            ->where('employee_setup.hired_date', '>=', $constraints['from'])
            ->where('employee_setup.hired_date', '<=', $constraints['to'])
            ->get();
        return $employees;
    }

    private function getExportingAttendanceData($constraints)
    {
        $query = DB::table('tk_employee_dtr_summary AS dtr')
            ->leftJoin('employees AS e', 'dtr.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->leftJoin('shift AS s', 'dtr.shift_id', '=', 's.id')
            ->leftJoin('account AS a', 'es.account_id', '=', 'a.id')
            ->leftJoin('team AS t', 'es.team_id', '=', 't.id')
            ->leftJoin('employee_overtime AS ot', function($join) {
                $join->on('dtr.employee_id', '=', 'ot.employee_id');
                $join->on('dtr.date', '=', 'ot.date');
            })
            ->leftJoin('employee_leave_dates AS eld', 'eld.date', '=', 'dtr.date')
            ->leftJoin('employee_leaves AS el', function($join) {
                $join->on('eld.employee_leave_id', '=', 'el.id');
                $join->on('dtr.employee_id', '=', 'el.employee_id');
            })
            ->where('dtr.date', '>=', $constraints['from'])
            ->where('dtr.date', '<=', $constraints['to'])
            ->select('e.employee_number', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS employee_name'), 'r.name AS role', 'a.name AS account', 't.name AS team', 's.start', 's.end', 'dtr.*', 'ot.datetime_from AS ot_start', 'ot.datetime_to AS ot_end', 'eld.leave_credit')
            ->orderBy('e.employee_number')
            ->orderBy('dtr.date')
            ->groupBy('dtr.employee_id', 'dtr.date')
            ->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();
        return $query;    
    }

    private function getExportingOvertimeData($constraints)
    {
        $query = DB::table('tk_employee_dtr_summary AS dtr')
            ->leftJoin('employees AS e', 'dtr.employee_id', '=', 'e.id')
            ->leftJoin('employee_overtime AS ot', function($join) {
                $join->on('dtr.employee_id', '=', 'ot.employee_id');
                $join->on('dtr.date', '=', 'ot.date');
            })
            ->where('dtr.date', '>=', $constraints['from'])
            ->where('dtr.date', '<=', $constraints['to'])
            ->select('e.employee_number', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS employee_name'), 'dtr.*')
            ->orderBy('e.employee_number')
            ->orderBy('dtr.date')
            ->groupBy('dtr.employee_id', 'dtr.date')
            ->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();
        return $query;    
    }

    private function getExportingLeaveData($constraints)
    {
        $query = DB::table('tk_employee_dtr_summary AS dtr')
            ->leftJoin('employees AS e', 'dtr.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
            ->leftJoin('roles AS r', 'es.role_id', '=', 'r.id')
            ->leftJoin('shift AS s', 'dtr.shift_id', '=', 's.id')
            ->leftJoin('account AS a', 'es.account_id', '=', 'a.id')
            ->leftJoin('team AS t', 'es.team_id', '=', 't.id')
            ->leftJoin('employee_overtime AS ot', function($join) {
                $join->on('dtr.employee_id', '=', 'ot.employee_id');
                $join->on('dtr.date', '=', 'ot.date');
            })
            ->leftJoin('employee_leave_dates AS eld', 'eld.date', '=', 'dtr.date')
            ->leftJoin('employee_leaves AS el', function($join) {
                $join->on('eld.employee_leave_id', '=', 'el.id');
                $join->on('dtr.employee_id', '=', 'el.employee_id');
            })
            ->where('dtr.date', '>=', $constraints['from'])
            ->where('dtr.date', '<=', $constraints['to'])
            ->select('e.employee_number', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS employee_name'), 'r.name AS role', 'a.name AS account', 't.name AS team', 's.start', 's.end', 'dtr.*', 'ot.datetime_from AS ot_start', 'ot.datetime_to AS ot_end', 'eld.leave_credit')
            ->orderBy('e.employee_number')
            ->orderBy('dtr.date')
            ->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();
        return $query;    
    }

    private function getExportingTardinessData($constraints)
    {
        // DB::enableQueryLog();
        $query = DB::table('tk_employee_dtr_summary AS dtr')
            ->leftJoin('employees AS e', 'dtr.employee_id', '=', 'e.id')
            ->leftJoin('employee_setup AS es', 'e.id', '=', 'es.employee_id')
            ->where('dtr.date', '>=', $constraints['from'])
            ->where('dtr.date', '<=', $constraints['to'])
            ->select('e.employee_number', DB::raw('CONCAT(e.firstname," ",e.lastname)  AS employee_name'), DB::raw('SUM(dtr.late)  AS late'), DB::raw('SUM(dtr.undertime) AS undertime'), DB::raw('SUM(dtr.absent)  AS absent'))
            ->groupBy('dtr.employee_id')
            ->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();
        // echo "<pre>";print_r(DB::getQueryLog());die("here");
        return $query;    
    }
}
