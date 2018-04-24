@extends('layouts.base', ['module' => 'Employee Manage'])
@section('action-content')
    <!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-sm-6">
            <h3 class="box-title">List of employees</h3>
          </div>
          <div class="col-sm-2">
            <!-- <a class="btn btn-primary" href="{{ route('employee-management.create') }}">Add new employee</a> -->
          </div>
          <div class="col-sm-4">
            <!-- <a class="btn btn-primary upload-employee">Mass Upload</a>  -->
            <form method="post" enctype="multipart/form-data" action="{{ route('employee-management.import') }}">
              {{ csrf_field() }}
              <div class="upload col-md-6">
                <input type="file" name="upload" accept="xls/*" id="fileUpload" />
                <span class="fileName">Select file..</span>
              </div>
              <div class="col-md-6">
                <button type="button" class="btn btn-primary col-sm-7 col-xs-9" style="padding: 4px 12px !important;"><i class="fa fa-upload"></i> Import</button>
              </div>
            </form>
          </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
          <div class="col-sm-6">
            <div class="alert alert-block">
            </div>
          </div>
          <div class="col-sm-6"></div>
        </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-sm-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr role="row">
                  <th width="1%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Picture: activate to sort column descending" aria-sort="ascending">Picture</th>
                  <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Name</th>
                  <th width="12%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Address: activate to sort column ascending">Address</th>
                  <th width="8%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending">Age</th>
                  <th width="8%" class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Birthdate</th>
                  <!-- <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th> -->
                </tr>
              </thead>
              <tbody>
              @foreach ($employees as $employee)
                  <tr role="row" class="odd">
                    <td><img src="../{{$employee->picture }}" width="50px" height="50px"/></td>
                    <td class="sorting_1">{{ $employee->firstname }} {{$employee->middlename}} {{$employee->lastname}}</td>
                    <td class="hidden-xs">{{ $employee->address }}</td>
                    <td class="hidden-xs">{{ $employee->age }}</td>
                    <td class="hidden-xs">{{ $employee->birthdate }}</td>
                    <!-- <td>
                      <form class="row" method="POST" action="{{ route('employee-management.destroy', ['id' => $employee->id]) }}" onsubmit = "return confirm('Are you sure?')">
                          <input type="hidden" name="_method" value="DELETE">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('employee-management.edit', ['id' => $employee->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                          Update
                          </a>
                           <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                            Delete
                          </button>
                      </form>
                    </td> -->
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($employees)}} of {{count($employees)}} entries</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $employees->links() }}
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

<!-- Modal -->
<!-- <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
  <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="uploadModalLabel">Mass Upload of Employee Details</h4>
      </div>
      <form class="form-horizontal approval-form" role="form" method="POST" action="{{ route('employee-management.import') }}">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="container">
            <div class="row">
                <div class="alert alert-block">
                </div>
                <div class="form-group col-md-12">
                  Choose your xls/csv File : <input type="file" name="file" class="form-control">
                  <button type="submit" class="btn btn-primary col-sm-3 col-xs-5 btn-margin"><i class="fa fa-upload"></i><span> Import</span>
                  </button>
                </div>
            </div>
          </div>
        </div>
      </form>  
  </div>
</div>    
<div id="content">
</div> -->