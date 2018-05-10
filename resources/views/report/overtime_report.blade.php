 <!DOCTYPE html>
<html lang="en">
  <body>
    <div class="containers">
        <div>Transcosmos Asia Philippines</div><br>
        <div>{{$report}}</div><br>
        <div>For the period {{date('d', strtotime($range->date_from))}} - {{date('d F Y', strtotime($range->date_to))}}</div>
        <br><br>
        <div>
          <table class="ot_reports" id="example2" role="grid" style="border: 1px bold solid;">
            <thead style="word-wrap: break-word;">
              <tr role="rows">
                <td colspan="2"></td>
                <td colspan="25" style="text-align: center"><b>OT Rate</b></td>
              </tr>
              <tr role="rows">
                <th rowspan="3" style="text-align: center;">Ee number</th>
                <th rowspan="3" style="text-align: center;">Ee Name</th>   
                <th colspan="2" style="text-align: center;">On Regular Day</th>
                <th colspan="3" style="text-align: center;">On Weekend/Restday</th>
                <th colspan="4" style="text-align: center;">Regular Holiday</th>
                <th colspan="4" style="text-align: center;">Regular Holiday on a Rest Day</th>
                <th colspan="4" style="text-align: center;">Special Holiday</th>
                <th colspan="4" style="text-align: center;">Special Holiday which falls on a Rest Day</th>
                <th colspan="4" style="text-align: center;">Double Holiday</th>

                <td rowspan="3" style="text-align: center;"><b>TOTAL OT HOURS</b></td>
                <th></th>
              </tr>
              <tr role="rows">  
                <td></td> 
                <td></td> 
                <td style="text-align: center;">before 10PM</td>
                <td style="text-align: center;">between 10PM - 6AM</td>

                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM</td>

                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (after 1st 8hrs)</td>

                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (after 1st 8hrs)</td>
                
                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (after 1st 8hrs)</td>
                
                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM</td>
                
                <td style="text-align: center;">before 10PM (1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM (1st 8hrs)</td>
                <td style="text-align: center;">before 10PM(after 1st 8hrs)</td>
                <td style="text-align: center;">between 10PM - 6AM</td>
              </tr>
              <tr role="rows" style="color: #ff0000;">   
                <td></td> 
                <td></td> 
                <td style="text-align: center;">125%</td>
                <td style="text-align: center;">137.5%</td>

                <td style="text-align: center;">130%</td>
                <td style="text-align: center;">169%</td>
                <td style="text-align: center;">143%</td>

                <td style="text-align: center;">100%</td>
                <td style="text-align: center;">120%</td>
                <td style="text-align: center;">260%</td>
                <td style="text-align: center;">286%</td>

                <td style="text-align: center;">260%</td>
                <td style="text-align: center;">286%</td>
                <td style="text-align: center;">338%</td>
                <td style="text-align: center;">372%</td>
                
                <td style="text-align: center;">30%</td>
                <td style="text-align: center;">43%</td>
                <td style="text-align: center;">169%</td>
                <td style="text-align: center;">185.90%</td>
                
                <td style="text-align: center;">150%</td>
                <td style="text-align: center;">165%</td>
                <td style="text-align: center;">195%</td>
                <td style="text-align: center;">214.50%</td>
                
                <td style="text-align: center;">200%</td>
                <td style="text-align: center;">230%</td>
                <td style="text-align: center;">390%</td>
                <td style="text-align: center;">429%</td>
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