@extends('layouts.base', ['module' => 'Employee Daily Time Record'])
@section('action-content')
    <!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="form-group">
        <!-- </div> -->
        <input type="hidden" name="dtrlog" value="{{route('dtr.dailyLog')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
          <div class="col-md-6">
            <label class="col-md-2 control-label">From</label>
            <div class="col-md-10">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="date_from" class="form-control pull-right datepicker" id="dtrFrom" required>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-1 control-label">To</label>
            <div class="col-md-10">
              <div class="input-group date">
                  <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="date_to" class="form-control pull-right datepicker" id="dtrTo" required>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
    <!-- /.box-header -->
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="employee-dtr" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="1%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending"></th>
                <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date</th>
                <!-- <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Day Type</th> -->
                <th width="11%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Shift</th>
                <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">IN</th>
                <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">OUT</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Hrs Work</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">NDiff (hrs)</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Late (hrs)</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">UT (hrs)</th>
                <th width="6%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">OT (hrs)</th>
                <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">OT Excess(hrs)</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Leave</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>      
  <!-- /.box-body -->
  </div>
</section>
    <!-- /.content -->
  </div>
@endsection