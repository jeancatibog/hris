@extends('layouts.base', ['module' => 'TAS Reports'])
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="POST" action="{{ route('report.excel') }}">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="col-md-4 control-label">Leave Type</label>
                <div class="col-md-4">
                  <select class="form-control" name="report_id" required>
                    <option value="">Please select your leave type</option>
                    <option value="1">Attendance Report</option>
                    <option value="2">Overtime Report</option>
                    <option value="3">Tardiness Report</option>
                    <option value="4">Leave Report</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                  <label class="col-md-4 control-label">Date From</label>
                  <div class="col-md-4">
                      <div class="input-group date">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" value="" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required>
                      </div>
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-md-4 control-label">Date To</label>
                  <div class="col-md-4">
                      <div class="input-group date">
                          <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" value="" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
                      </div>
                  </div>
              </div>
              <div class="form-group">
                <div class="col-md-9 col-md-offset-5">
                  <button type="submit" class="btn btn-primary">
                    Export to Excel
                  </button>
                </div>
              </div>
              
          </form>
        </div>
      </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection