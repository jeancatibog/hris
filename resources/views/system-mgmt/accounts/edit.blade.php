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
                                    @foreach ($teamleads as $tl)
                                        <option {{$acctTL->team_lead_id == $tl->id ? 'selected' : ''}} value="{{$tl->id}}">{{$tl->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>




                        <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname" class="col-md-4 control-label">First Name</label>

                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control" name="firstname" value="{{ $employee->firstname }}" required autofocus>

                                @if ($errors->has('firstname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                            <label for="lastname" class="col-md-4 control-label">Last Name</label>

                            <div class="col-md-6">
                                <input id="lastname" type="text" class="form-control" name="lastname" value="{{ $employee->lastname }}" required>

                                @if ($errors->has('lastname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('middlename') ? ' has-error' : '' }}">
                            <label for="middlename" class="col-md-4 control-label">Middle Name</label>

                            <div class="col-md-6">
                                <input id="middlename" type="text" class="form-control" name="middlename" value="{{ $employee->middlename }}" required>

                                @if ($errors->has('middlename'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('middlename') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Country</label>
                            <div class="col-md-6">
                                <select class="form-control js-country" name="country_id">
                                    <option value="-1">Please select your country</option>
                                    @foreach ($countries as $country)
                                        <option {{$employee->country_id == $country->id ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Province</label>
                            <div class="col-md-6">
                                <select class="form-control js-provinces" name="province_id">
                                    <option value="-1">Please select your province</option>
                                    @foreach ($provinces as $province)
                                        <option {{$employee->province_id == $province->id ? 'selected' : ''}} value="{{$province->id}}">{{$province->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">City</label>
                            <div class="col-md-6">
                                <select class="form-control js-cities" name="city_id">
                                    <option value="-1">Please select your city</option>
                                    @foreach ($cities as $city)
                                        <option {{$employee->city_id == $city->id ? 'selected' : ''}} value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label for="address" class="col-md-4 control-label">Address</label>

                            <div class="col-md-6">
                                <input id="address" type="text" class="form-control" name="address" value="{{ $employee->address }}" required>

                                @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('zip') ? ' has-error' : '' }}">
                            <label for="zip" class="col-md-4 control-label">Zip</label>

                            <div class="col-md-6">
                                <input id="zip" type="text" class="form-control" name="zip" value="{{ $employee->zip }}" required>

                                @if ($errors->has('zip'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('zip') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('age') ? ' has-error' : '' }}">
                            <label for="zip" class="col-md-4 control-label">Age</label>

                            <div class="col-md-6">
                                <input id="age" type="text" class="form-control" name="age" value="{{ $employee->age }}" required>

                                @if ($errors->has('age'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('age') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Birthday</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ $employee->birthdate }}" name="birthdate" class="form-control pull-right" id="birthDate" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="avatar" class="col-md-4 control-label" >Picture</label>
                            <div class="col-md-6">
                                <img src="../../{{$employee->picture }}" width="50px" height="50px"/>
                                <input type="file" id="picture" name="picture" />
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
