@extends('dashboard.base')
@section('action-content')
    <!-- Main content -->
<section class="content">
  <div class="box">
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        @include('layouts.flash-message')
        @include('dashboard.dashboard-bundy')
    </div>
  <!-- /.box-body -->
  </div>
</section>
    <!-- /.content -->
  </div>
@endsection