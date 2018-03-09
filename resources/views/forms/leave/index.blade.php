<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr role="row">
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="leave type: activate to sort column ascending">Leave Type</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date from: activate to sort column ascending">Date From</th>
      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="date to: activate to sort column ascending">Date To</th>
      <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="reason: activate to sort column ascending">Reason</th>
      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="status: activate to sort column ascending">Status</th>
      <th tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Action</th>
    </tr>
  </thead>
  <tbody>
  {{$leaves}}
  @foreach ($leaves as $leave)
      <tr role="row" class="odd">
        <td>{{ $leave->form }}</td>
        <td>{{ $leave->date_from }}</td>
        <td>{{ $leave->date_to }}</td>
        <td>{{ $leave->reason }}</td>
        <td>{{ $leave->status }}</td>
        <td>
          <form class="row" method="POST" action="{{ route('forms.destroy', ['id' => $leave->id]) }}" onsubmit = "return confirm('Are you sure?')">
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              @if($leave->status == 'Draft')  
                <a href="{{ route('forms.edit', ['id'=>$leave->id, 'form'=>'leave']) }}" class="btn btn-warning col-sm-3 col-xs-5 btn-margin">
                Update
                </a>
                <button type="submit" class="btn btn-danger col-sm-3 col-xs-5 btn-margin delete">
                  Delete
                </button>
              @elseif($leave->status == 'For Approval')
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