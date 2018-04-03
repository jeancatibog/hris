@extends('layouts.base', ['module' => 'Daily Time Record Problem Form'])

@section('action-content')
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
          <div class="panel-heading">Log Time In/Out</div>
          <div class="panel-body">
              <form class="form-horizontal file-form" role="form" method="POST" action="{{ route('forms.store') }}">
                  {{ csrf_field() }}

                  <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                  <input type="hidden" name="ftype" value="dtrp">
                  <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                      <label class="col-md-4 control-label">Date</label>
                      <div class="col-md-6">
                          <div class="input-group date">
                              <div class="input-group-addon">
                                  <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" value="{{ old('date') }}" name="date" class="form-control pull-right datepicker" id="date" required>
                          </div>
                      @if ($errors->has('date'))
                          <span class="help-block">
                              <strong>{{ $errors->first('date') }}</strong>
                          </span>
                      @endif
                      </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Log Type</label>
                    <div class="col-md-6">
                      <select class="form-control" name="log_type" required>
                        <option value="1">Time In</option>
                        <option value="2">Time Out</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group{{ $errors->has('timelog') ? ' has-error' : '' }}">
                      <label class="col-md-4 control-label">Actual Time</label>
                      <div class="col-md-6">
                          <div class="input-group bootstrap-timepicker">
                              <div class="input-group-addon">
                                  <i class="fa fa-clock-o"></i>
                              </div>
                              <input type="text" value="{{ old('datetime_from') }}" name="timelog" class="form-control pull-right timepicker" id="timeLog" required>
                          </div>
                      @if ($errors->has('timelog'))
                          <span class="help-block">
                              <strong>{{ $errors->first('timelog') }}</strong>
                          </span>
                      @endif
                      </div>
                  </div>
                  <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                      <label for="reason" class="col-md-4 control-label">Reason</label>
                      <div class="col-md-6">
                        <textarea class="form-control" rows="5" id="reason" name="reason" required></textarea>
                      @if ($errors->has('reason'))
                          <span class="help-block">
                              <strong>{{ $errors->first('reason') }}</strong>
                          </span>
                      @endif
                      </div>
                  </div>
                  <!-- Buttons -->
                  @include ('layouts.file-form-buttons')
              </form>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection