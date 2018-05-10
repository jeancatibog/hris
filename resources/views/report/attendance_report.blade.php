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
                <th colspan="12" style="text-align: center;"></th>
                <th colspan="3" style="text-align: center;">OT hours</th>
                <th colspan="1" style="text-align: center;">Tardiness/Undertime</th>
                <th colspan="2" style="text-align: center;">Leave</th>
              </tr>
              <tr role="rows">
                <th width="15%" style="text-align: center;">Date</th>
                <th width="10%" style="text-align: center;">Day</th>
                <th width="20%" style="text-align: center;">Classification</th>
                <th width="15%" style="text-align: center;">Ee number</th>
                <th width="25%" style="text-align: center;">Ee Name</th>
                <th width="20%" style="text-align: center;">Role Title</th> 
                <th width="20%" style="text-align: center;">Shift</th>
                <th width="20%" style="text-align: center;">Team</th>
                <th width="20%" style="text-align: center;">Account</th>
                <th width="15%" style="text-align: center;">Time-in</th>
                <th width="15%" style="text-align: center;">Time-out</th> 
                <th width="20%" style="text-align: center;">Actual Total ND (in Hours)</th>
                <th width="15%" style="text-align: center;">Time-in</th>
                <th width="15%" style="text-align: center;">Time-out</th>
                <th width="15%" style="text-align: center;">Total Approved OT hours</th>
                <th width="15%" style="text-align: center;">in Hours</th>           
                <th width="20%" style="text-align: center;">Type of Leave</th>
                <th width="15%" style="text-align: center;">No. of Leave Days</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $ot = 0;
                $tardy = 0;
                $leave = 0;
                $old = '';
                $old_name = '';
                $day_type = '';
                $ot_sub = 0;
                $tardy_sub = 0;
                $ot_total = 0;
                $tardy_total = 0;
                $leave_total = 0;
                $leave_sub = 0;
              ?>
                
              @foreach ($data as $key => $record)
                @if ($record['employee_number'] != $old && $key != 0)
                  <tr>
                    <td colspan="14"><b>Total - {{ $old }} {{ $old_name }}</b></td>
                    <td>{{ $ot_sub }}</td>
                    <td>{{  $tardy_sub }}</td>
                    <td></td>
                    <td>{{  $leave_sub }}</td>
                  </tr>
                  <tr></tr>
                <?php
                  $ot_sub = 0;
                  $tardy_sub = 0;
                  $leave_sub = 0;
                ?>
                @endif

                <?php
                  if ( $record['day_type'] == 'reg' ) {
                    $day_type = 'Regular Working Day';
                  } else if ( $record['day_type'] == 'rd' ) {
                    $day_type = 'Restday';
                  } else if ( $record['day_type'] == 'leg' ) {
                    $day_type = 'Regular Holiday';
                  } else if ( $record['day_type'] == 'spl' ) {
                    $day_type = 'Special Holiday';
                  } else if ( $record['day_type'] == 'legrd' ) {
                    $day_type = 'Restday / Regular Holiday';
                  } else if ( $record['day_type'] == 'splrd' ) {
                    $day_type = 'Restday / Special Holiday';
                  } else {
                    $day_type = 'Restday / Double Holiday';
                  }

                  $ot = $record['ot_hours'] + $record['ot_excess'] + $record['ndot'] + $record['ndot_excess'] + $record['legot'] + $record['legot_excess'] + $record['leg_ndot'] + $record['leg_ndot_excess'] + $record['splot'] + $record['splot_excess'] + $record['spl_ndot'] + $record['spl_ndot_excess'];
                  
                  $tardy = $record['late'] + $record['undertime'];

                  $leave = $record['leave_credit'];
                
                  $ot_sub += $ot;
                  $tardy_sub += $tardy;
                  $leave_sub += $leave;
                  $ot_total += $ot;
                  $tardy_total += $tardy;
                  $leave_total += $leave;
                ?>
                <tr role="row" class="odd">
                  <td style="text-align: right;">{{ date("d F Y", strtotime($record['date'])) }}</td>
                  <td>{{ date("l", strtotime($record['date'])) }}</td>
                  <td>{{ $day_type }}</td>
                  <td style="text-align: center;">{{ $record['employee_number'] }}</td>
                  <td>{{ $record['employee_name'] }}</td>
                  <td>{{ $record['role'] }}</td>
                  <td>{{ date("h:i A", strtotime($record['start'])) }} - {{ date("h:i A", strtotime($record['end'])) }}</td>
                  <td>{{ $record['team'] }}</td>
                  <td>{{ $record['account'] }}</td>
                  <td style="text-align: center;">{{ $record['absent'] != 1 ? date("h:i A", strtotime($record['time_in'])) : '' }} </td>
                  <td style="text-align: right;">{{ $record['absent'] != 1 ? date("h:i A", strtotime($record['time_out'])) : '' }}</td>
                  <td>{{ $record['ndiff'] }}</td>
                  <td style="text-align: center;">{{ !empty($record['ot_start']) ? date("h:i A", strtotime($record['ot_start'])) : '' }}</td>
                  <td style="text-align: center;">{{ !empty($record['ot_end']) ? date("h:i A", strtotime($record['ot_end'])) : '' }}</td>
                  <td style="text-align: right;">{{ $ot }}</td>
                  <td style="text-align: right;">{{ $tardy }}</td>
                  <td style="text-align: center;">{{ $record['leave_type'] }}</td>
                  <td style="text-align: right;">{{ $record['leave_credit'] }}</td>
                </tr>
                <?php
                  $old = $record['employee_number'];
                  $old_name = $record['employee_name'];
                ?>
              @endforeach
              <tr>
                <td colspan="14"><b>Total - {{ $old }} {{ $old_name }}</b></td>
                <td>{{ $ot_sub }}</td>
                <td>{{  $tardy_sub }}</td>
                <td></td>
                <td>{{  $leave_sub }}</td>
              </tr>
              <tr></tr>
              <tr>
                <td colspan="14"><b>GRAND TOTAL</b></td>
                <td>{{ $ot_total }}</td>
                <td>{{  $tardy_total }}</td>
                <td></td>
                <td>{{  $leave_total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
    </div>
  </body>
</html>