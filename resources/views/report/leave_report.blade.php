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
                
              </tr>
              <tr role="rows">
                <th rowspan="2" style="text-align: center;">Ee number</th>
                <th rowspan="2" style="text-align: center;">Ee Name</th>       
                <th rowspan="2" style="text-align: center;">Hire Date</th>
                <th rowspan="2" style="text-align: center;">Regularization Date</th>
                <th rowspan="2" style="text-align: center;">Classification</th>
                <th rowspan="2" style="text-align: center;">Leave Credits</th>
                <th rowspan="2" style="text-align: center;">Earned Leaves (until Dec 31)</th>
              </tr>
            </thead>
            <tbody>
                
              @foreach ($data as $key => $record)
                <tr role="row" class="odd">
                  <td style="text-align: center;">{{ $record['employee_number'] }}</td>
                  <td>{{ $record['employee_name'] }}</td>
                  <td style="text-align: right;">{{ $tardy }}</td>
                  <td style="text-align: right;">{{ $record['absent'] }}</td>
                </tr>
              @endforeach
              <tr>
                <td colspan="2"><b>GRAND TOTAL</b></td>
                <td>{{  $tardy_total }}</td>
                <td>{{ $absent_total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
    </div>
  </body>
</html>