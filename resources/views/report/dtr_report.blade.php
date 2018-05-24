 <!DOCTYPE html>
<html lang="en">
  <body>
    <div class="containers">
        <div>Transcosmos Asia Philippines</div><br>
        <div>{{$report}}</div><br>
        <div>For the period {{date('d', strtotime($range->date_from))}} - {{date('d F Y', strtotime($range->date_to))}}</div>
        <br><br>
        <div>
          <table class="reports" id="example2" role="grid">
            <thead style="word-wrap: break-word;">
              <tr role="rows">
                <th style="text-align: center;">Ee number</th>
                <th style="text-align: center;">Ee Name</th>
                <th style="text-align: center;">Date</th>
                <th style="text-align: center;">Shift</th>
                <th style="text-align: center;">IN</th>
                <th style="text-align: center;">OUT</th>
                <th style="text-align: center;">Hrs Work</th>
                <th style="text-align: center;">NDiff (hrs)</th>
                <th style="text-align: center;">Late (hrs)</th> 
                <th style="text-align: center;">UT (hrs)</th>
                <th style="text-align: center;">OT (hrs)</th>
                <th style="text-align: center;">OT Excess(hrs)</th>
                <th style="text-align: center;">Leave</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($data as $dtr)
              <tr>
                <td>
                  {{ $dtr->employee_number }}
                </td>
                <td style="text-align: left;">
                  {{ $dtr->fullname }}
                </td>
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
                  <td colspan="10" style="text-align: center;">ABSENT</td>
                @elseif ($dtr->day_type == 'rd')
                  <td colspan="10" style="text-align: center;">RESTDAY</td>
                @elseif ($dtr->leave)
                  <td colspan="10" >{{$dtr->leave_type}}</td>
                @elseif (!empty($dtr->holiday))
                  <td colspan="10" style="text-align: center;">
                    {{$dtr->holiday}}( @if ($dtr->day_type == 'splrd' || $dtr->day_type == 'spl') Special Holiday @elseif ($dtr->day_type == 'legrd' || $dtr->day_type == 'leg')  Legal Holiday @else Double Holiday @endif )
                  </td>
                @endif
              </tr>  
            @endforeach
            </tbody>
          </table>
        </div>
    </div>
  </body>
</html>