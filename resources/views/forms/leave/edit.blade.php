@extends('layouts.base', ['module' => 'Leave Form'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update leave form</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('forms.update', ['id' => $form->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="employee_id" value="{{ $form->employee_id }}">
                        <input type="hidden" name="ftype" value="leave">
                        <div class="form-group{{ $errors->has('form_type_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Leave Type</label>
                            <div class="col-md-6">
                                <select class="form-control" name="form_type_id" required>
                                    <option value="">Please select your leave type</option>
                                    @foreach ($types as $type)
                                        <option {{$form->form_type_id == $type->id ? 'selected' : ''}} value="{{$type->id}}">{{$type->form}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('form_type_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('form_type_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('date_from') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Date From</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ $form->date_from }}" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required>
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
                                    <input type="text" value="{{ $form->date_to }}" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
                                </div>
                            @if ($errors->has('date_to'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('date_to') }}</strong>
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
                        <div class="form-group">
                            <label class="col-md-4 control-label">Is Halfday</label>
                            <div class="col-md-1">
                                <input type="checkbox" name="is_halfday" class="form-check-input" id="is-halfday" {{$form->is_halfday == 1 ? 'checked' : ''}}>
                            </div>
                            <div class="col-md-5 halfday" style="display: none;">
                                <select class="form-control" name="halfday_type">
                                    <option value="1" {{$form->halfday_type == 1 ? 'selected' : ''}}>1st Half Day</option>
                                    <option value="2" {{$form->halfday_type == 2 ? 'selected' : ''}}>2nd Half Day</option>
                                </select>
                            </div>
                        </div>
                        @include ('layouts.file-form-buttons')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
