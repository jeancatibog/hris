<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="18%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="name: activate to sort column ascending">Employee Name</th>
      <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="name: activate to sort column ascending">Role</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="leave type: activate to sort column ascending">Leave Type</th>
      <th width="9%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date From</th>
      <th width="9%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date to: activate to sort column ascending">Date To</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($leaves as $leave)
      <tr role="row" class="odd">
        <td>{{ $leave->name }}</td>
        <td>{{ $leave->role }}</td>
        <td>{{ $leave->form }}</td>
        <td>{{ $leave->date_from }}</td>
        <td>{{ $leave->date_to }}</td>
        <td>{{ $leave->reason }}</td>
        <td>{{ $leave->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('form-approval.destroy', ['id' => $leave->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
              <meta name="csrf-token" content="{{ csrf_token() }}">
              <input type="hidden" name="form_url" value="{{ route('form-approval.edit', ['id' => $leave->id, 'form'=>'leave']) }}">
              <a href="#" class="btn btn-warning col-sm-4 col-xs-5 btn-margin approval-update">
                Update
              </a>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>

@include ('form-approval.approval-modal', ['id' => isset($leave->id) ? $leave->id : '', 'form' => 'leave'])