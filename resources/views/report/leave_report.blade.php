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
                <th rowspan="2" style="text-align: center;">Ee number</th>
                <th rowspan="2" style="text-align: center;">Ee Name</th>       
                <th rowspan="2" style="text-align: center;">Hire Date</th>
                <th rowspan="2" style="text-align: center; word-wrap: break-word;">Regularization Date</th>
                <th rowspan="2" style="text-align: center; word-wrap: break-word;">Classification</th>
                <th rowspan="2" style="text-align: center;">Leave Credits</th>
                <th rowspan="2" style="text-align: center; word-wrap: break-word;">Earned Leaves (until Dec 31)</th>
              <?php foreach (array_column($data, 'leave') as $key => $value) {
                        foreach ($value as $k => $val) {
                            $arr[$k] = $val;
                        }
                    } //echo "<pre>";print_r($arr);die("here"); ?>
              @foreach ($arr as $header => $colspan)
                <?php
                  $ret = '';
                  foreach (explode(' ', $header) as $word)
                      $ret .= strtoupper($word[0]);
                ?>
                <th colspan="{{count($colspan)+1}}" style="text-align: center;">{{($header)}} Taken</th>
                <th rowspan="2">{{$ret}} Balance</th>
              @endforeach
              </tr>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              @foreach ($arr as $header => $colspan)
                <?php
                  $ret = '';
                  foreach (explode(' ', $header) as $word)
                      $ret .= strtoupper($word[0]);
                ?>
                @foreach ($colspan as $month => $days)
                  <th>{{$month}}</th>
                @endforeach
                  <th style="word-wrap: break-word;">Total {{$ret}} Taken</th>
                  <th></th>
              @endforeach
              </tr>
            </thead>
            <tbody>
            @foreach ($data as $key =>$record)
              <tr>
                <td>{{ $record['employee_number'] }}</td>
                <td>{{ $record['employee_name'] }}</td>
                <td>{{ $record['hired_date'] }}</td>
                <td <?php if ($record['status'] == 'Probationary') { ?> style="color:#FF0000" <?php } ?>>{{ $record['regularization_date'] }}</td>
                <td>{{ $record['status'] }}</td>
                <td>15</td>
                @if($record['status'] == 'Probationary')
                  <td style="color:#FF0000">{{$record['earned_leave']}}</td>
                @else
                  <td>15</td>
                @endif
              @foreach ($record['leave'] as $form => $months)
              <?php $total_days = 0; ?>
                @foreach ($months as $month => $days)
                  <?php $total_days += $days; ?>

                   @foreach ($arr as $header => $colspan)
                    @foreach ($colspan as $mo => $day)
                      @if($mo == $month && $header == $form)
                        <td>{{ $days }}</td>
                        <?php break; ?>
                      @endif
                    @endforeach
                  @endforeach
                  
                @endforeach
                <td>{{$total_days}}</td>
                <td>
                  @if($record['status'] == 'Probationary')
                    {{ $record['earned_leave'] - $total_days}}
                  @else
                    {{ 15 - $total_days }}
                  @endif
                </td><!-- leave balance -->
              @endforeach
              </tr>
            @endforeach 
            </tbody>
          </table>
        </div>
    </div>
  </body>
</html>