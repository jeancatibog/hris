@extends('layouts.base', ['module' => 'Employee Daily Time Record'])
@section('action-content')
    <!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="form-group">
        <!-- </div> -->
          <div class="col-md-6">
            <label class="col-md-2 control-label">From</label>
            <div class="col-md-10">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="date_from" class="form-control pull-right datepicker" id="from" required>
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
                  <input type="text" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
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
            <tbody>
            @foreach ($employeeDtr as $dtr)
              <tr role="row" class="odd">
                @if($dtr->day_type == 'rd' || $dtr->day_type == 'sperd' || $dtr->day_type == 'legrd' ) 
                  <td>
                      <icon class="fa fa-bed" style="color: #dd4b39;"></icon>
                  </td>    
                @else
                  <td></td>
                @endif 
                <td>
                  {{ date("Y M d", strtotime($dtr->date)) }}
                </td>
                <!-- <td>{{ $dtr->day_type }}</td> -->
                @if ( ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && !$dtr->absent && !$dtr->leave) || ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && $dtr->leave) || ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && $dtr->holiday) ||  ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') || (!empty($dtr->time_in) && $dtr->time_in != '00:00:00')) )
                  <td>{{ date('h:i A', strtotime($dtr->start)) }} - {{ date('h:i A', strtotime($dtr->end)) }}</td>
                  <td>{{ date("h:i A", strtotime($dtr->time_in)) }}</td>
                  <td>{{ date("h:i A", strtotime($dtr->time_out)) }}</td>
                  <td>{{ $dtr->hours_work }}</td>
                  <td>{{ $dtr->ndiff }}</td>
                  <td>{{ $dtr->late }}</td>
                  <td>{{ $dtr->undertime }}</td>
                  <td>{{ $dtr->ot_hours }}</td>
                  <td>{{ $dtr->ot_excess }}</td>
                  <td>{{$dtr->leave_type}}</td>
                @elseif ($dtr->absent)
                  <td colspan="10" >ABSENT</td>
                @elseif ($dtr->day_type == 'rd')
                  <td colspan="10" >RESTDAY</td>
                @elseif ($dtr->leave)
                  <td colspan="10" >{{$dtr->leave_type}}</td>
                @elseif (!empty($dtr->holiday))
                  <td colspan="10" >{{$dtr->holiday}}</td>
                @endif
              </tr>  
            @endforeach
            </tbody>
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