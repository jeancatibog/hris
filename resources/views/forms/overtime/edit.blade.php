@extends('layouts.base', ['module' => 'Overtime Form'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update overtime form</div>
                <div class="panel-body">
                    <form class="form-horizontal edit-form" role="form" method="POST" action="{{ route('forms.update', ['id' => $form->id, 'action_id']) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="employee_id" value="{{ $form->employee_id }}">
                        <input type="hidden" name="ftype" value="ot">
                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Date</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ $form->date }}" name="date" class="form-control pull-right datepicker" id="date" required>
                                </div>
                            @if ($errors->has('date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('date') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('datetime_from') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Actual OT Start</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-datetimepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $form->datetime_from }}" name="datetime_from" class="form-control pull-right datetimepicker" id="datetimeFrom" required>
                                </div>
                            @if ($errors->has('datetime_from'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('datetime_from') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('datetime_to') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Actual OT End</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-datetimepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $form->datetime_to }}" name="datetime_to" class="form-control pull-right datetimepicker" id="datetimeTo" required>
                                </div>
                            @if ($errors->has('datetime_to'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('datetime_to') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Reason</label>
                            <div class="col-md-6">
                                <textarea class="form-control" rows="5" id="reason" name="reason" required>{{ $form->reason }}</textarea>
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
