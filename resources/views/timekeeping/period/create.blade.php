@extends('layouts.base', ['module' => 'Period Coverage'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new period cover</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('timekeeping.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Period Start</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('start_date') }}" name="start_date" class="form-control pull-right datepicker" id="startDate" required>
                                </div>
                            @if ($errors->has('start_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Period End</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('end_date') }}" name="end_date" class="form-control pull-right datepicker" id="endDate" required>
                                </div>
                            @if ($errors->has('end_date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('end_date') }}</strong>
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