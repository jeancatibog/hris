@extends('layouts.base', ['module' => 'OBT Form'])

@section('action-content')

{{$disabled = (isset($for_approval) && $for_approval) ? 'disabled' : ''}}
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update obt form</div>
                <div class="panel-body">
                    <form class="form-horizontal edit-form" role="form" method="POST" action="{{ route('forms.update', ['id' => $form->id, 'action_id']) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="employee_id" value="{{ $form->employee_id }}">
                        <input type="hidden" name="ftype" value="obt">
                        <div class="form-group{{ $errors->has('date_from') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Date From</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$form->date_from}}" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required {{$disabled}}>
                                </div>
                            @if ($errors->has('date_from'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('date_from') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('date_to') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Date To</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{$form->date_to}}" name="date_to" class="form-control pull-right datepicker" id="datetimeTo" required {{$disabled}}>
                                </div>
                            @if ($errors->has('date_to'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('date_to') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('starttime') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Start Time</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{$form->starttime}}" name="starttime" class="form-control pull-right timepicker" id="startTime" required {{$disabled}}>
                                </div>
                            @if ($errors->has('starttime'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('starttime') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('endtime') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">End Time</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{$form->endtime}}" name="endtime" class="form-control pull-right timepicker" id="endTime" required {{$disabled}}>
                                </div>
                            @if ($errors->has('endtime'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('endtime') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Reason</label>
                            <div class="col-md-6">
                                <textarea class="form-control" rows="5" id="reason" name="reason" required {{$disabled}}> {{$form->reason}}</textarea>
                            @if ($errors->has('reason'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('reason') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <hr>
                        <div class="form-group{{ $errors->has('company_to_visit') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Company Name</label>
                            <div class="col-md-6">
                                <input type="text" value="{{$form->company_to_visit}}" class="form-control" rows="5" id="company_to_visit" name="company_to_visit" required {{$disabled}}>
                            @if ($errors->has('company_to_visit'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company_to_visit') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('company_location') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Company Location</label>
                            <div class="col-md-6">
                                <input type="text" value="{{$form->company_location}}" class="form-control" rows="5" id="company_location" name="company_location" required {{$disabled}}>
                            @if ($errors->has('company_location'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company_location') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('contact_name') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Contact Person</label>
                            <div class="col-md-6">
                                <input type="text" value="{{$form->contact_name}}" class="form-control" rows="5" id="contact_name" name="contact_name" required {{$disabled}}>
                            @if ($errors->has('contact_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_name') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('contact_info') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Contact Number</label>
                            <div class="col-md-6">
                                <input type="text" value="{{$form->contact_info}}" class="form-control" rows="5" id="contact_info" name="contact_info" required {{$disabled}}>
                            @if ($errors->has('contact_info'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_info') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('contact_position') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Contact Position</label>
                            <div class="col-md-6">
                                <input type="text" value="{{$form->contact_position}}" class="form-control" rows="5" id="contact_position" name="contact_position" required {{$disabled}}>
                            @if ($errors->has('contact_position'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact_position') }}</strong>
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
