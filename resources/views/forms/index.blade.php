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
          <a id="file_form" class="btn btn-primary" href="{{ route('forms.create', ['form' => 'leave']) }}">File new form</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="">
         {{ csrf_field() }}
         @component('layouts.search', ['title' => 'Search'])
          @component('layouts.two-cols-search-row', ['items' => ['Form Type'], 
          'oldVals' => [isset($searchingVals) ? $searchingVals['form'] : '']])
          @endcomponent
        @endcomponent
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" id="formsTab">
            <li class="active"><a id="leave" href="#leaves-tab" data-toggle="tab" aria-expanded="false"><span>Leaves</span></a></li>
            <li><a id="overtime" href="#ot-tab" data-toggle="tab" aria-expanded="true"><span>Overtime</span></a></li>
            <li><a id="obt" href="#obt-tab" data-toggle="tab" aria-expanded="true"><span>OBT</span></a></li>
        </ul>
      </div>
      <!-- Tab contents -->
      <div class="tab-content responsive">
        <div class="tab-pane active" id="leaves-tab">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="leave type: activate to sort column ascending">Leave Type</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date From</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date to: activate to sort column ascending">Date To</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </thead>
            <tbody>
            {{$forms['leaves']}}
            @foreach ($forms['leaves'] as $leave)
                <tr role="row" class="odd">
                  <td>{{ $leave->form }}</td>
                  <td>{{ $leave->date_from }}</td>
                  <td>{{ $leave->date_to }}</td>
                  <td>{{ $leave->reason }}</td>
                  <td>
                    <form class="row" method="POST" action="{{ route('forms.destroy', ['id' => $leave->id]) }}" onsubmit = "return confirm('Are you sure?')">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('city.edit', ['id' => $city->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                        Update
                        </a>
                        <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                          Delete
                        </button>
                    </form>
                  </td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="leave type: activate to sort column ascending">Leave Type</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date From</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date to: activate to sort column ascending">Date To</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="tab-pane" id="ot-tab">Overtime</div>
        <div class="tab-pane" id="obt-tab">Official Business Trip</div>
      </div>
      <!-- </div> -->
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($forms)}} of {{count($forms)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection