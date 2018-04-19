<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="1%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"></th>
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Employee Name</th>
      <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date</th>
      <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime start: activate to sort column ascending">Actual Start</th>
      <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime end: activate to sort column ascending">Actual End</th>
      <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($overtime as $ot)
      <tr role="row" class="odd">
        @if($ot->is_restday == 1 ) 
          <td>
              <icon class="fa fa-bed" style="color: #dd4b39;"></icon>
          </td>    
        @else
          <td></td>
        @endif
        <td>{{ $ot->name }}</td>
        <td>{{ $ot->date }}</td>
        <td>{{ date("Y-m-d h:i A", strtotime($ot->datetime_from)) }}</td>
        <td>{{ date("Y-m-d h:i A", strtotime($ot->datetime_to)) }}</td>
        <td>{{ $ot->reason }}</td>
        <td>{{ $ot->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('form-approval.destroy', ['id' => $ot->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">  -->
              <meta name="csrf-token" content="{{ csrf_token() }}">
              <input type="hidden" name="form_url" value="{{ route('form-approval.edit', ['id' => $ot->id, 'form'=>'overtime']) }}">
              <a href="#" class="btn btn-warning col-sm-5 col-xs-5 btn-margin approval-update">
                Update
              </a>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>

@include ('form-approval.approval-modal', ['id' => isset($ot->id) ? $ot->id : '', 'form' => 'overtime'])