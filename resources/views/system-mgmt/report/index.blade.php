@extends('layouts.base', ['module' => 'TAS Reports'])
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <!-- <div class="col-sm-4">
          <h3 class="box-title">List of hired employees</h3>
        </div> -->
        <!-- <div class="col-sm-4">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('report.pdf') }}">
                {{ csrf_field() }}
                <input type="hidden" value="{{$searchingVals['from']}}" name="from" />
                <input type="hidden" value="{{$searchingVals['to']}}" name="to" />
                <button type="submit" class="btn btn-info">
                  Export to PDF
                </button>
            </form>
        </div> -->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="POST" action="{{ route('report.excel') }}">
              {{ csrf_field() }}
              <!-- <input type="hidden" value="{{$searchingVals['from']}}" name="from" />
              <input type="hidden" value="{{$searchingVals['to']}}" name="to" /> -->
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
    <!-- <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width = "20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Employee Name</th>
                <th width = "20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthday: activate to sort column ascending">Birthday</th>
                <th width = "40%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Address: activate to sort column ascending">Address</th>
                <th width = "20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthday: activate to sort column ascending">Hired Day</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($employees as $employee)
                <tr role="row" class="odd">
                  <td>{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->lastname }}</td>
                  <td>{{ $employee->birthdate }}</td>
                  <td>{{ $employee->address }}</td>
                  <td>{{ $employee->date_hired }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($employees)}} of {{count($employees)}} entries</div>
        </div>
      </div>
    </div> -->
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection