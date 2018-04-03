@extends('layouts.base', ['module' => 'Dashboard'])
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

<!-- Modal -->
<div class="modal fade" id="logtimeModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="logtimeModalLabel">Log Time Warning</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-block">
          </div>
          <div>
            <input type="hidden" name="date">
            <input type="hidden" name="ctype">
          </div>
          <span class="msg-log"></span>
        </div>
        <div class="modal-footer">
          <div class="col-sm-7">
            <a id="file_form" class="btn btn-primary" href="{{ route('forms.create', ['form' => 'dtrp']) }}">Log </a>
          </div>
        </div>
</div>
<div id="content">
</div>
<div class="loading">
    <i class="fa fa-refresh fa-spin fa-2x fa-fw"></i><br/>
    <span>Loading</span>
</div>
@endsection