@extends('layouts.base', ['module' => 'Accounts Team Lead Manage'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new team lead account schedule</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('accounts.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('account_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Account</label>
                            <div class="col-md-6">
                                <select class="form-control js-account" name="account_id" required>
                                	<option value="">Please select your account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account->id}}">{{$account->name}}</option>
                                    @endforeach
                                </select>

							  	@if ($errors->has('account_id'))
							      	<span class="help-block">
							          	<strong>{{ $errors->first('account_id') }}</strong>
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
                                    <input type="text" value="{{ old('date_from') }}" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required>
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
                                    <input type="text" value="{{ old('date_to') }}" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
                                </div>
                            @if ($errors->has('date_to'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('date_to') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Team Lead</label>
                            <div class="col-md-6">
                                <select class="form-control js-team-lead" name="team_lead_id">
                                    <option value="-1">Please select team lead</option>
                                    {{--  @foreach ($teamleads as $tl)
                                        <option value="{{$tl->id}}">{{$tl->name}}</option>
                                    @endforeach  --}}
                                </select>
                            </div>
                        </div>
                        <!-- Buttons -->
                        @include ('layouts.default-buttons')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
