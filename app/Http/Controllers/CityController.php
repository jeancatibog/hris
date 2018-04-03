<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\City;
use App\Province;

class CityController extends Controller
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
         $cities = DB::table('city')
        ->leftJoin('province', 'city.province_id', '=', 'province.id')
        ->select('city.id', 'city.name', 'province.name as province_name', 'province.id as province_id')
        ->paginate(5);
        return view('system-mgmt/city/index', ['cities' => $cities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all();
        return view('system-mgmt/city/create', ['provinces' => $provinces]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Province::findOrFail($request['province_id']);
        $this->validateInput($request);
         city::create([
            'name' => $request['name'],
            'province_id' => $request['province_id']
        ]);

        return redirect()->intended('system-management/city');
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
        $city = city::find($id);
        // Redirect to city list if updating city wasn't existed
        if ($city == null || count($city) == 0) {
            return redirect()->intended('/system-management/city');
        }

        $province = Province::all();
        return view('system-mgmt/city/edit', ['city' => $city, 'provinces' => $province]);
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
        $city = City::findOrFail($id);
         $this->validate($request, [
        'name' => 'required|max:60'
        ]);
        $input = [
            'name' => $request['name'],
            'province_id' => $request['province_id']
        ];
        City::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/city');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        City::where('id', $id)->delete();
         return redirect()->intended('system-management/city');
    }

    public function loadCities($provinceId) {
        $cities = City::where('province_id', $provinceId)->get();
        
        return response()->json($cities);
    }

    /**
     * Search city from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $constraints = [
            'name' => $request['name']
            ];

       $cities = $this->doSearchingQuery($constraints);
       return view('system-mgmt/city/index', ['cities' => $cities, 'searchingVals' => $constraints]);
    }
    
    private function doSearchingQuery($constraints) {
        $query = City::query();
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
            'name' => 'required|max:60|unique:city'
        ]);
    }
}
