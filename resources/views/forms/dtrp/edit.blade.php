@extends('layouts.base', ['module' => 'Daily Time Record Problem Form'])

@section('action-content')

{{$disabled = (isset($for_approval) && $for_approval) ? 'disabled' : ''}}
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
          <div class="panel-heading">Update Log Time In/Out</div>
          <div class="panel-body">
              <form class="form-horizontal file-form" role="form" method="POST" action="{{ route('forms.store') }}">
                  {{ csrf_field() }}

                  <input type="hidden" name="employee_id" value="{{ $form->employee_id }}">
                  <input type="hidden" name="ftype" value="dtrp">
                  <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                      <label class="col-md-4 control-label">Date</label>
                      <div class="col-md-6">
                          <div class="input-group date">
                              <div class="input-group-addon">
                                  <i class="fa fa-calendar"></i>
                              </div>
                              <input type="text" value="{{ $form->date }}" name="date" class="form-control pull-right datepicker" id="date" required {{$disabled}}>
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
                      <select class="form-control" name="log_type" required {{$disabled}}>
                        <option value="1" {{$form->log_type_id == 1 ? 'selected' : '' }} >Time In</option>
                        <option value="2" {{$form->log_type_id == 2 ? 'selected' : '' }} >Time Out</option>
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
                              <input type="text" value="{{ $form->timelog }}" name="timelog" class="form-control pull-right timepicker" id="timeLog" required {{$disabled}}>
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
                        <textarea class="form-control" rows="5" id="reason" name="reason" required {{$disabled}}>{{ $form->reason }}</textarea>
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