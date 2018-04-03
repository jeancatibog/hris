<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

Auth::routes();

Route::get('/dashboard', 'DashboardController@index');

Route::resource('dashboard', 'DashboardController');
// Route::get('/system-management/{option}', 'SystemMgmtController@index');
Route::get('/profile', 'ProfileController@index');

Route::post('user-management/search', 'UserManagementController@search')->name('user-management.search');
Route::resource('user-management', 'UserManagementController');

Route::resource('employee-management', 'EmployeeManagementController');
Route::post('employee-management/search', 'EmployeeManagementController@search')->name('employee-management.search');

Route::resource('system-management/department', 'DepartmentController');
Route::post('system-management/department/search', 'DepartmentController@search')->name('department.search');

Route::resource('system-management/division', 'DivisionController');
Route::post('system-management/division/search', 'DivisionController@search')->name('division.search');

Route::resource('system-management/country', 'CountryController');
Route::post('system-management/country/search', 'CountryController@search')->name('country.search');

Route::resource('system-management/province', 'ProvinceController');
Route::post('system-management/province/search', 'ProvinceController@search')->name('province.search');
Route::get('get-province-list/{countryId}', 'ProvinceController@loadProvinces');

Route::resource('system-management/city', 'CityController');
Route::post('system-management/city/search', 'CityController@search')->name('city.search');
Route::get('get-city-list/{provinceId}', 'CityController@loadCities');

Route::get('system-management/report', 'ReportController@index');
Route::post('system-management/report/search', 'ReportController@search')->name('report.search');
Route::post('system-management/report/excel', 'ReportController@exportExcel')->name('report.excel');
Route::post('system-management/report/pdf', 'ReportController@exportPDF')->name('report.pdf');

Route::get('avatars/{name}', 'EmployeeManagementController@load');

Route::resource('system-management/shift', 'ShiftController');
Route::post('system-management/shift/search', 'ShiftController@search')->name('shift.search');

Route::resource('employee-setup-management', 'EmployeeSetupManagementController');

Route::resource('forms', 'FormsController');
Route::post('forms/search', 'FormsController@search')->name('forms.search');
Route::post('/forms/{id}', 'FormsController@store');

Route::resource('timekeeping/period', 'TimekeepingController');
Route::post('timekeeping/log', 'TimekeepingController@log')->name('timekeeping.log');
Route::get('timekeeping/period/create', 'TimekeepingController@create')->name('timekeeping.create');
Route::post('timekeeping/period/store', 'TimekeepingController@store')->name('timekeeping.store');
Route::post('timekeeping/period/{id}/edit', 'TimekeepingController@edit')->name('timekeeping.edit');
Route::post('timekeeping/period/destroy', 'TimekeepingController@destroy')->name('timekeeping.destroy');
Route::post('timekeeping/period/update', 'TimekeepingController@update')->name('timekeeping.update');

Route::resource('dtr', 'DtrController');
// Route::post('dtr', 'DtrController@dailyLog')->name('dtr.dailyLog');
Route::post('dtr/dailyLog', 'DtrController@dailyLog')->name('dtr.dailyLog');

// Route::resource('timekeeping/process', 'TimekeepingController@process');
Route::get('timekeeping/process', 'TimekeepingController@process')->name('timekeeping.process');
Route::post('timekeeping/processing', 'TimekeepingController@processing')->name('timekeeping.processing');
