@extends('layouts.base', ['module' => 'Overtime Free Day Form'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new overtime free day form</div>
                <div class="panel-body">
                    <form class="form-horizontal file-form" role="form" method="POST" action="{{ route('forms.store') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                        <input type="hidden" name="ftype" value="ofd">
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
                        <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Start</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ old('start') }}" name="start" class="form-control pull-right timepicker" id="datetimeFrom" required>
                                </div>
                            @if ($errors->has('start'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">End</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ old('end') }}" name="end" class="form-control pull-right timepicker" id="datetimeTo" required>
                                </div>
                            @if ($errors->has('end'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('end') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Reason</label>
                            <div class="col-md-6">
                            	<textarea class="form-control" rows="5" id="reason" name="reason" placeholder="Please include in the reason the date to be in liue of your overtime free day." required></textarea>
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
