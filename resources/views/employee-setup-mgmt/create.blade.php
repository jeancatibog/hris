@extends('employee-setup-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add employee setup</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('employee-setup-management.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="form-group">
                            <label class="col-md-4"></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ $employee->lastname . ", " . $employee->firstname }}" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Date Hired</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('date_hired') }}" name="date_hired" class="form-control pull-right datepicker" id="dateHired" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Role</label>
                            <div class="col-md-6">
                                <select class="form-control" name="role_id">
                                    <option value="">Please select employee role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Division</label>
                            <div class="col-md-6">
                                <select class="form-control" name="division_id">
                                    <option value="">Please select division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{$division->id}}">{{$division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Department</label>
                            <div class="col-md-6">
                                <select class="form-control" name="department_id">
                                    <option value="">Please select department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Approver</label>
                            <div class="col-md-6">
                                <select class="form-control" name="approver_id">
                                    <option value="">Please select approver</option>
                                    @foreach ($approvers as $approver)
                                        <option value="{{$approver->id}}">{{$approver->lastname . ", " . $approver->firstname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Reports To</label>
                            <div class="col-md-6">
                                <select class="form-control" name="reports_to_id">
                                    <option value="">Please select supervisor</option>
                                    @foreach ($reports_to as $reports)
                                        <option value="{{$reports->id}}">{{$reports->lastname . ", " . $reports->firstname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Default Shift</label>
                            <div class="col-md-6">
                                <select class="form-control" name="shift_id">
                                    <option value="">Please select employee shift</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{$shift->id}}">{{date('h:i A',strtotime($shift->start)) . "-" . date('h:i A',strtotime($shift->end))}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('position') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Position</label>
                            <div class="col-md-6">
                                <input type="text" name="position" class="form-control" value="{{ old('position') }}">
                                @if ($errors->has('position'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('position') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Job Title</label>
                            <div class="col-md-6">
                                <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                                @if ($errors->has('job_tile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('job_tile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('vl_credits') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Vacation Leave Credits</label>
                            <div class="col-md-6">
                                <input type="text" name="vl_credits" class="form-control" value="{{ old('vl_credits') }}">
                                @if ($errors->has('vl_credits'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('vl_credits') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sl_credits') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Sick Leave Credits</label>
                            <div class="col-md-6">
                                <input type="text" name="sl_credits" class="form-control" value="{{ old('sl_credits') }}">
                                @if ($errors->has('sl_credits'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sl_credits') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('bil_credits') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Bday Leave Credits</label>
                            <div class="col-md-6">
                                <input type="text" name="bil_credits" class="form-control" value="{{ old('bil_credits') }}">
                                @if ($errors->has('bil_credits'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('bil_credits') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('el_credits') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Emergency Leave Credits</label>
                            <div class="col-md-6">
                                <input type="text" name="el_credits" class="form-control" value="{{ old('el_credits') }}">
                                @if ($errors->has('el_credits'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('el_credits') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        @include('layouts.default-buttons')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
