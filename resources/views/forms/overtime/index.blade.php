<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date</th>
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime start: activate to sort column ascending">Actual Start</th>
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime end: activate to sort column ascending">Actual End</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  {{$overtime}}
  @foreach ($overtime as $ot)
      <tr role="row" class="odd">
        <td>{{ $ot->date }}</td>
        <td>{{ date("Y-m-d h:i A", strtotime($ot->datetime_from)) }}</td>
        <td>{{ date("Y-m-d h:i A", strtotime($ot->datetime_to)) }}</td>
        <td>{{ $ot->reason }}</td>
        <td>{{ $ot->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('forms.destroy', ['id' => $ot->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              @if($ot->status == 'Draft')
                <a href="{{ route('forms.edit', ['id' => $ot->id]) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                Update
                </a>
              @endif  
              <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin">
                Delete
              </button>
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>