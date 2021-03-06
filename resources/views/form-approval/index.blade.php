@extends('layouts.base', ['module' => 'Forms for Approval'])
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of forms for approval</h3>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" id="approvalFormsTab">
            <li class="active"><a id="leaves" href="#leaves-tab" data-toggle="tab" aria-expanded="false"><span>Leaves</span></a></li>
            <li><a id="overtime" href="#overtime-tab" data-toggle="tab" aria-expanded="true"><span>Overtime</span></a></li>
            <li><a id="obt" href="#obt-tab" data-toggle="tab" aria-expanded="true"><span>OBT</span></a></li>
            <li><a id="dtrp" href="#dtrp-tab" data-toggle="tab" aria-expanded="true"><span>DTRP</span></a></li>
          @if ($form_approval['role'] != 'Team Lead' && $form_approval['role'] != 'Senior Associate' && $form_approval['role'] != 'Associate')  
            <li><a id="ofd" href="#ofd-tab" data-toggle="tab" aria-expanded="true"><span>OFD</span></a></li>
          @endif
        </ul>
      </div>
      <!-- Tab contents -->
      <div class="tab-content responsive">
        <div class="tab-pane active" id="leaves-tab">
          @include ('form-approval.index-leave', [ 'leaves' => $form_approval['leaves'] ])
          @include ('layouts.pagination', ['data' => $form_approval['leaves']])
        </div>
        <div class="tab-pane" id="overtime-tab">
          @include ('form-approval.index-overtime', [ 'overtime' => $form_approval['ot'] ])
          @include ('layouts.pagination', ['data' => $form_approval['ot']])
        </div>
        <div class="tab-pane" id="obt-tab">
          @include ('form-approval.index-obt', [ 'obt' => $form_approval['obt'] ])
          @include ('layouts.pagination', ['data' => $form_approval['obt']])
        </div>
        <div class="tab-pane" id="dtrp-tab">
          @include ('form-approval.index-dtrp', [ 'dtrp' => $form_approval['dtrp'] ])
          @include ('layouts.pagination', ['data' => $form_approval['dtrp']])
        </div>
        <!-- for supervisor and up only -->
        <div class="tab-pane" id="ofd-tab">
          @include ('form-approval.index-ofd', [ 'ofd' => $form_approval['ofd'] ])
          @include ('layouts.pagination', ['data' => $form_approval['ofd']])
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection