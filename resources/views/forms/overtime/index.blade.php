<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="1%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"></th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date</th>
      <th width="19%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime start: activate to sort column ascending">Actual Start</th>
      <th width="19%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="datetime end: activate to sort column ascending">Actual End</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  {{$overtime}}
  @foreach ($overtime as $ot)
      <tr role="row" class="odd">
        @if($ot->is_restday == 1 ) 
          <td>
              <icon class="fa fa-bed" style="color: #dd4b39;"></icon>
          </td>    
        @else
          <td></td>
        @endif
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
                <a href="{{ route('forms.edit', ['id' => $ot->id, 'form'=>'overtime']) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                Update
                </a>
                <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin delete">
                  Delete
                </button>
              @elseif($ot->status == 'For Approval')
                <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin cancel">
                  Cancel
                </button>
              @endif  
          </form>
        </td>
    </tr>
  @endforeach
  </tbody>
</table>