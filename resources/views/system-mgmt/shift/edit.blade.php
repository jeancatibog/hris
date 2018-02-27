@extends('system-mgmt.shift.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update shift</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('shift.update', ['id' => $shift->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Shift Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $shift->name }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shift_start" class="col-md-4 control-label">Shift Start</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $shift->start }}" name="start" class="form-control pull-right timepicker" id="shift_start" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shift_end" class="col-md-4 control-label">Shift End</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $shift->end }}" name="end" class="form-control pull-right timepicker" id="shift_end" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="first_halfday_end" class="col-md-4 control-label">End of First Half</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $shift->first_halfday_end }}" name="first_halfday_end" class="form-control pull-right timepicker" id="first_halfday_end" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="second_halfday_start" class="col-md-4 control-label">Start of Second Half</label>
                            <div class="col-md-6">
                                <div class="input-group bootstrap-timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" value="{{ $shift->second_halfday_start }}" name="second_halfday_start" class="form-control pull-right timepicker" id="second_halfday_start" required>
                                </div>
                            </div>
                        </div>
                        @include('layouts.update-buttons')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
