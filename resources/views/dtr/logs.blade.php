@foreach ($employeeDtr as $dtr)
  <tr role="row" class="odd">
    @if($dtr->day_type == 'rd' || $dtr->day_type == 'splrd' || $dtr->day_type == 'legrd' ) 
      <td>
          <icon class="fa fa-bed" style="color: #dd4b39;"></icon>
      </td>    
    @else
      <td></td>
    @endif 
    <td>
      {{ date("Y M d", strtotime($dtr->date)) }}
    </td>
    <!-- <td>{{ $dtr->day_type }}</td> -->
    @if ( ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && !$dtr->absent && !$dtr->leave) || ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && $dtr->leave) || ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') && (!empty($dtr->time_out) && $dtr->time_out != '00:00:00') && $dtr->holiday) ||  ((!empty($dtr->time_in) && $dtr->time_in != '00:00:00') || (!empty($dtr->time_in) && $dtr->time_in != '00:00:00')) )
      <td>{{ date('h:i A', strtotime($dtr->start)) }} - {{ date('h:i A', strtotime($dtr->end)) }}</td>
      <td>{{ date("h:i A", strtotime($dtr->time_in)) }}</td>
      <td>{{ date("h:i A", strtotime($dtr->time_out)) }}</td>
      <td>{{ $dtr->hours_work }}</td>
      <td>{{ $dtr->ndiff }}</td>
      <td>{{ $dtr->late }}</td>
      <td>{{ $dtr->undertime }}</td>
      <td>{{ ($dtr->ot_hours + $dtr->legot + $dtr->splot) + ($dtr->ndot + $dtr->leg_ndot + $dtr->spl_ndot) }}</td>
      <td>{{ ($dtr->ot_excess + $dtr->legot_excess + $dtr->splot_excess) + ($dtr->ndot_excess + $dtr->leg_ndot_excess + $dtr->spl_ndot_excess) }}</td>
      <td>{{$dtr->leave_type}}</td>
    @elseif ($dtr->absent)
      <td colspan="10" >ABSENT</td>
    @elseif ($dtr->day_type == 'rd')
      <td colspan="10" >RESTDAY</td>
    @elseif ($dtr->leave)
      <td colspan="10" >{{$dtr->leave_type}}</td>
    @elseif (!empty($dtr->holiday))
      <td colspan="10" >
        {{$dtr->holiday}}( @if ($dtr->day_type == 'splrd' || $dtr->day_type == 'spl') Special Holiday @elseif ($dtr->day_type == 'legrd' || $dtr->day_type == 'leg')  Legal Holiday @else Double Holiday @endif )
      </td>
    @endif
  </tr>  
@endforeach