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
                        @include('layouts.default-buttons')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
