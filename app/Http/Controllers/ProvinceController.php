<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Province;
use App\Country;

class ProvinceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provinces = DB::table('province')
        ->leftJoin('country', 'province.country_id', '=', 'country.id')
        ->select('province.id', 'province.name', 'country.name as country_name', 'country.id as country_id')
        ->paginate(5);
        return view('system-mgmt/province/index', ['provinces' => $provinces]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('system-mgmt/province/create', ['countries' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Country::findOrFail($request['country_id']);
        $this->validateInput($request);
         Province::create([
            'name' => $request['name'],
            'country_id' => $request['country_id']
        ]);

        return redirect()->intended('system-management/province');
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
        $province = Province::find($id);
        // Redirect to province list if updating province wasn't existed
        if ($province == null || count($province) == 0) {
            return redirect()->intended('/system-management/province');
        }

        $countries = Country::all();
        return view('system-mgmt/province/edit', ['province' => $province, 'countries' => $countries]);
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
        $province = Province::findOrFail($id);
         $this->validate($request, [
        'name' => 'required|max:60'
        ]);
        $input = [
            'name' => $request['name'],
            'country_id' => $request['country_id']
        ];
        Province::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/province');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        province::where('id', $id)->delete();
         return redirect()->intended('system-management/province');
    }

    public function loadProvinces($countryId) {
        $provinces = Province::where('country_id', '=', $countryId)->get(['id', 'name']);

        return response()->json($provinces);
    }
    
    /**
     * Search province from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $constraints = [
            'name' => $request['name']
            ];

       $provinces = $this->doSearchingQuery($constraints);
       return view('system-mgmt/province/index', ['provinces' => $provinces, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = Province::query();
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
    private function validateInput($request) {
        $this->validate($request, [
        'name' => 'required|max:60|unique:province'
    ]);
    }
}
