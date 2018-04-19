<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Employee Name</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date From</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date to: activate to sort column ascending">Date To</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime start: activate to sort column ascending">Start Time</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime end: activate to sort column ascending">Start End</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($obt as $obts)
      <tr role="row" class="odd">
        <td>{{ $obts->name }}</td>
        <td>{{ $obts->date_from }}</td>
        <td>{{ $obts->date_to }}</td>
        <td>{{ date("h:i A", strtotime($obts->starttime)) }}</td>
        <td>{{ date("h:i A", strtotime($obts->endtime)) }}</td>
        <td>{{ $obts->reason }}</td>
        <td>{{ $obts->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('form-approval.destroy', ['id' => $obts->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="form_url" value="{{ route('form-approval.edit', ['id' => $obts->id, 'form'=>'obt']) }}">
              <a href="#" class="btn btn-warning col-sm-4 col-xs-5 btn-margin approval-update">
                Update
              </a>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>

@include ('form-approval.approval-modal', ['id' => isset($obts->id) ? $obts->id : '', 'form' => 'obt'])