@extends('layouts.base', ['module' => 'Overtime Form'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new overtime form</div>
                <div class="panel-body">
                    <form class="form-horizontal file-form" role="form" method="POST" action="{{ route('forms.store') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                        <input type="hidden" name="ftype" value="ot">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Date</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('date') }}" name="date" class="form-control pull-right datepicker" id="date" required>

	                                @if ($errors->has('date'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('date') }}</strong>
	                                    </span>
	                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Actual OT Start</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-datetimepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ old('datetime_from') }}" name="datetime_from" class="form-control pull-right datetimepicker" id="datetimeFrom" required>

	                                @if ($errors->has('datetime_from'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('datetime_from') }}</strong>
	                                    </span>
	                                @endif
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Actual OT End</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-datetimepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ old('datetime_to') }}" name="datetime_to" class="form-control pull-right datetimepicker" id="datetimeTo" required>

                                    @if ($errors->has('datetime_to'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('datetime_to') }}</strong>
                                        </span>
                                    @endif
                                </div>

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
                        <div class="form-group">
						    <div class="col-md-6 col-md-offset-4">
						        <button type="button" class="btn btn-default btn-back" onclick="history.back(1)">
								    Cancel
								</button>
								<button type="submit" class="btn btn-success" id="draft">
								    Save as Draft
								</button>
								<button type="submit" class="btn btn-primary" id="save">
								    Submit
								</button>
						    </div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
