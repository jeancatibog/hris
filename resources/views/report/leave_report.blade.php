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
                <th colspan="2" style="text-align: center;"></th>
                <th style="text-align: center;">Tardiness</th>
                <th style="text-align: center;">Absent/UL</th>
              </tr>
              <tr role="rows">
                <th width="15%" style="text-align: center;">Ee number</th>
                <th width="25%" style="text-align: center;">Ee Name</th>       
                <th width="20%" style="text-align: center;">in Hours</th>
                <th width="15%" style="text-align: center;">in Days</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tardy_total = 0;
                $absent_total = 0;
              ?>
                
              @foreach ($data as $key => $record)
                <?php
                  $tardy = number_format($record['late'], 2, '.', '') + number_format($record['undertime'], 2, '.','');
                  $tardy_total += $tardy;
                  $absent_total += $record['absent'];
                ?>
                <tr role="row" class="odd">
                  <td style="text-align: center;">{{ $record['employee_number'] }}</td>
                  <td>{{ $record['employee_name'] }}</td>
                  <td style="text-align: right;">{{ $tardy }}</td>
                  <td style="text-align: right;">{{ $record['absent'] }}</td>>
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