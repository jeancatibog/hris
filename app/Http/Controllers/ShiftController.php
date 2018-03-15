<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Shift;

class ShiftController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::paginate(5);
        return view('system-mgmt/shift/index', ['shifts' => $shifts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('system-mgmt/shift/create');
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
        Shift::create([
            'name'  =>  $request['name'],
            'start' =>  date("H:i",strtotime($request['start'])),
            'end'   =>  date("H:i",strtotime($request['end'])),
            'first_halfday_end' =>  date("H:i",strtotime($request['first_halfday_end'])),
            'second_halfday_start'  =>  date("H:i",strtotime($request['second_halfday_start']))
        ]);

        return redirect()->intended('system-management/shift');
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
        $shift = Shift::find($id);
        $shift['start'] = date('h:i A', strtotime($shift['start']));
        $shift['end'] = date('h:i A', strtotime($shift['end']));
        $shift['first_halfday_end'] = date('h:i A', strtotime($shift['first_halfday_end']));
        $shift['second_halfday_start'] = date('h:i A', strtotime($shift['second_halfday_start']));
        return view('system-mgmt/shift/edit', ['shift' => $shift]);
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
        $input = [
            'name' => $request['name'],
            'start' =>  date("H:i",strtotime($request['start'])),
            'end'   =>  date("H:i",strtotime($request['end'])),
            'first_halfday_end' =>  date("H:i",strtotime($request['first_halfday_end'])),
            'second_halfday_start'  =>  date("H:i",strtotime($request['second_halfday_start']))
        ];
        Shift::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/shift');
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
    
    /**
     * Search shift from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $constraints = [
            'name' => $request['name']
            ];

       $shifts = $this->doSearchingQuery($constraints);
       return view('system-mgmt/shift/index', ['shifts' => $shifts, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Shift::query();
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

    private function validateInput($request, $id = NULL) {
        $this->validate($request, [
            'name'  =>  'required',
            'start' =>  'required',
            'end'   =>  'required'
        ]);
        
        // check if unique name for department
        $shift = Shift::where('name', $request['name'])->first();
        if(isset($id) && $shift->id != $id) {
            $this->validate($request, [
                'name' => 'unique:shift'
            ]);
        }  
    }
}
