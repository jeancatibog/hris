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
                <th rowspan="2" style="text-align: center;">Regularization Date</th>
                <th rowspan="2" style="text-align: center;">Classification</th>
                <th rowspan="2" style="text-align: center;">Leave Credits</th>
                <th rowspan="2" style="text-align: center;">Earned Leaves (until Dec 31)</th>
              <?php foreach (array_column($data, 'leave') as $key => $value) {
                        foreach ($value as $k => $val) {
                            $arr[$k] = $val;
                        }
                    } ?>
              @foreach ($arr as $header => $colspan)
                <th colspan="{{count($colspan)+1}}" style="text-align: center;">{{$header}} Taken</th>
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
                @foreach ($colspan as $month => $days)
                  <th>{{$month}}</th>
                @endforeach
                  <th>Total {{$header}} Taken</th>
              @endforeach
              </tr>
            </thead>
            <tbody>
            @foreach ($data as $key =>$record)
              <tr>
                <td>{{ $record['employee_number'] }}</td>
                <td>{{ $record['employee_name'] }}</td>
                <td>{{ $record['hired_date'] }}</td>
                <td>{{ $record['regularization_date'] }}</td>
                <td>{{ $record['status'] }}</td>
                <td></td>
                <td></td>
              @foreach ($record['leave'] as $form => $month)
              <?php $total_days = 0; ?>
                @foreach ($month as $days)
                  <?php $total_days += $days; ?>
                  <td>{{ $days }}</td>
                @endforeach
                <td>{{$total_days}}</td>
              @endforeach
              </tr>
            @endforeach 
            </tbody>
          </table>
        </div>
    </div>
  </body>
</html>