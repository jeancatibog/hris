@extends('layouts.base', ['module' => 'Accounts Team Lead Manage'])

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Accounts Team Lead</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('accounts.update', ['id' => $acctTL->id]) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        
                        <div class="form-group{{ $errors->has('account_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Account</label>
                            <div class="col-md-6">
                                <select class="form-control js-account" name="account_id" required>
                                    <option value="">Please select your account</option>
                                    @foreach ($accounts as $account)
                                        <option {{$acctTL->account_id == $account->id ? 'selected' : ''}} value="{{$account->id}}">{{$account->name}}</option>
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
                                    <input type="text" value="{{ $acctTL->date_from }}" name="date_from" class="form-control pull-right datepicker" id="dateFrom" required>
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
                                    <input type="text" value="{{ $acctTL->date_to }}" name="date_to" class="form-control pull-right datepicker" id="dateTo" required>
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
                                    @foreach ($teamleads as $tl)
                                        <option {{$acctTL->team_lead_id == $tl->id ? 'selected' : ''}} value="{{$tl->id}}">{{$tl->name}}</option>
                                    @endforeach
                                </select>
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
