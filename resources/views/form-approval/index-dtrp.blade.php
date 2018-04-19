<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="name: activate to sort column ascending">Employee Name</th>
      <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date: activate to sort column ascending">Date</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="log type: activate to sort column ascending">Type</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="timelog: activate to sort column ascending">Time</th>
      <th width="19%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($dtrp as $log)
      <tr role="row" class="odd">
        <td>{{ $log->name }}</td>
        <td>{{ $log->date }}</td>
        <td>{{ $log->log_type_id == 1 ? 'Time In' : 'Time Out' }}</td>
        <td>{{ date("Y-m-d h:i A", strtotime($log->timelog)) }}</td>
        <td>{{ $log->reason }}</td>
        <td>{{ $log->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('form-approval.destroy', ['id' => $log->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
              <meta name="csrf-token" content="{{ csrf_token() }}">
              <input type="hidden" name="form_url" value="{{ route('form-approval.edit', ['id' => $log->id, 'form'=>'dtrp']) }}">
              <a href="#" class="btn btn-warning col-sm-4 col-xs-5 btn-margin approval-update">
                Update
              </a>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>

@include ('form-approval.approval-modal', ['id' => isset($log->id) ? $log->id : '', 'form' => 'dtrp'])