@extends('layouts.base', ['module' => 'File Form'])
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">List of filed forms</h3>
        </div>
        <div class="col-sm-4">
          <a id="file_form" class="btn btn-primary" href="{{ route('forms.create', ['form' => 'leave']) }}">File new leave form</a>
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
        <ul class="nav nav-tabs" id="formsTab">
            <li class="active"><a id="leave" href="#leaves-tab" data-toggle="tab" aria-expanded="false"><span>Leaves</span></a></li>
            <li><a id="overtime" href="#ot-tab" data-toggle="tab" aria-expanded="true"><span>Overtime</span></a></li>
            <li><a id="obt" href="#obt-tab" data-toggle="tab" aria-expanded="true"><span>OBT</span></a></li>
            <li><a id="ofd" href="#ofd-tab" data-toggle="tab" aria-expanded="true"><span>OFD</span></a></li>
        </ul>
      </div>
      <!-- Tab contents -->
      <div class="tab-content responsive">
        <div class="tab-pane active" id="leaves-tab">
          @include ('forms.leave.index', [ 'leaves' => $forms['leaves'] ])
          @include ('layouts.pagination', ['data' => $forms['leaves']])
        </div>
        <div class="tab-pane" id="ot-tab">
          @include ('forms.overtime.index', [ 'overtime' => $forms['ot'] ])
          @include ('layouts.pagination', ['data' => $forms['ot']])
        </div>
        <div class="tab-pane" id="obt-tab">
          @include ('forms.obt.index', [ 'obt' => $forms['obt'] ])
          @include ('layouts.pagination', ['data' => $forms['obt']])
        </div>
        <div class="tab-pane" id="ofd-tab">
          @include ('forms.ofd.index', [ 'obt' => $forms['ofd'] ])
          @include ('layouts.pagination', ['data' => $forms['ofd']])
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