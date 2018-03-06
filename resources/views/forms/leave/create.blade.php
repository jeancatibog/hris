@extends('layouts.base', ['module' => 'Leave Form'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new leave form</div>
                <div class="panel-body">
                    <form class="form-horizontal file-form" role="form" method="POST" action="{{ route('forms.store') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="employee_id" value="{{ $employee_id }}">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Leave Type</label>
                            <div class="col-md-6">
                                <select class="form-control" name="form_type_id" required>
                                	<option value="">Please select your leave type</option>
                                    @foreach ($forms as $form)
                                        <option value="{{$form->id}}">{{$form->form}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Date From</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('date_from') }}" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Date To</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('date_to') }}" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label for="reason" class="col-md-4 control-label">Reason</label>
                            <div class="col-md-6">
                            	<textarea class="form-control" rows="5" id="reason" name="reason" required></textarea>
                                <!-- <input id="reason" type="text" class="form-control" name="reason" value="{{ old('reason') }}" required autofocus> -->

                                @if ($errors->has('reason'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Is Halfday</label>
                            <div class="col-md-1">
                            	<input type="checkbox" class="form-check-input" id="is-halfday">
                            	<input type="hidden" name="is_halfday">
                            </div>
                            <div class="col-md-5 halfday" style="display: none;">
                                <select class="form-control" name="form_type_id">
                                	<option value="0">1st Half Day</option>
                                	<option value="1">2nd Half Day</option>
                                </select>
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
