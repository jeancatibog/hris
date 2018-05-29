<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="1%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"></th>
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Employee Name</th>
      <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date</th>
      <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime start: activate to sort column ascending">Start</th>
      <th width="14%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime end: activate to sort column ascending">End</th>
      <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  @foreach ($ofd as $free)
      <tr role="row" class="odd">
        <td>{{ $free->name }}</td>
        <td>{{ $free->date }}</td>
        <td>{{ date("h:i A", strtotime($free->start)) }}</td>
        <td>{{ date("h:i A", strtotime($free->end)) }}</td>
        <td>{{ $free->reason }}</td>
        <td>{{ $free->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('form-approval.destroy', ['id' => $free->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">  -->
              <meta name="csrf-token" content="{{ csrf_token() }}">
              <input type="hidden" name="form_url" value="{{ route('form-approval.edit', ['id' => $free->id, 'form'=>'ofd']) }}">
              <a href="#" class="btn btn-warning col-sm-5 col-xs-5 btn-margin approval-update">
                Update
              </a>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>

@include ('form-approval.approval-modal', ['id' => isset($free->id) ? $free->id : '', 'form' => 'ofd'])