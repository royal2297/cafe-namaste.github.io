<?php
date_default_timezone_set('Asia/Kolkata');
$start = isset($_GET['start']) ? $_GET['start'] : date('20:00:00');
$end = isset($_GET['end']) ? $_GET['end'] : date('23:59:00');
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<html>
<div>
                <label>Date</label>
                <div>
                    <input type="date" name="date" id="date" onchange="foo_date()" value="<?php echo $date ?>"/>
                </div>
                <br />

                <label>Start Time</label>
                <div>
                    <input type="time" name="start" id="start" onchange="foo_start()" value="<?php echo $start ?>"/>
                </div>
                <br />
                <label>End Time</label>
                <div>
                    <input type="time" name="end" id="end" onchange="foo_end()" value="<?php echo $end ?>"/>
                </div>
                <br />
</div>

</html>
<script>
function foo_date(){
var date = document.getElementById("date").value;
var start = document.getElementById("start").value;
var end = document.getElementById("end").value;
location.replace('hourly_report.php?start='+start+'&end='+end+'&date='+date)
}

function foo_start(){
var start = document.getElementById("start").value;
var end = document.getElementById("end").value;
location.replace('hourly_report.php?start='+start+'&end='+end)
}
function foo_end(){
var start = document.getElementById("start").value;
var end = document.getElementById("end").value;
location.replace('hourly_report.php?end='+end+'&start='+start)
}

</script>

<?php
include('rms.php');
session_start();
$object = new rms();
$sum_object = new rms();
if(!$object->is_login())
{
    header("location:".$object->base_url."");
}
$output = '<table width="100%" border="1" cellpadding="5" cellspacing="5" style="font-family:Arial, san-sarif">';

// $object->query = "
// SELECT * FROM order_table
// WHERE  hour(order_time)  BETWEEN '".date("H", strtotime($start))."' AND '".date("H", strtotime($end))."'
// AND order_date = '".$date."'
// AND  order_status = 'Completed'";

// $sum_object->query = "
// SELECT Sum(order_net_amount) AS 'Total' FROM order_table
// WHERE  hour(order_time)  BETWEEN '".date("H", strtotime($start))."' AND '".date("H", strtotime($end))."'
// AND order_date = '".$date."'
// AND order_status = 'Completed'";

$object->query = "
SELECT * FROM order_table
WHERE  order_time  BETWEEN '".$start."' AND '".$end."'
AND order_date = '".$date."'
AND  order_status = 'Completed'";

$sum_object->query = "
SELECT Sum(order_net_amount) AS 'Total' FROM order_table
WHERE  order_time  BETWEEN '".$start."' AND '".$end."'
AND order_date = '".$date."'
AND  order_status = 'Completed'";



$upi_data = $object->get_result();
$total_amt = $sum_object->get_result();
//
$output .= '<tr>
                <th width="10%">Sr#</th>
                <th width="10%">Bill No.</th>
                <th width="10%">Bill Time</th>
                <th width="10%">Bill Date</th>
                <th width="15%">Bill Amount</th>
            </tr>';

$count = 0;
foreach($upi_data as $row)
{
$count++;
$output .= '<tr>
                <td>'.$count.'</td>
                <td>'.$row["order_number"].'</td>
                <td>'.$row["order_time"].'</td>
                <td>'.$row["order_date"].'</td>
                <td>'.$row["order_net_amount"].'</td>
            </tr>';

}
foreach($total_amt as $some_row){
$output .= '<tr>
            <th colspan=3 ><b>Total</b></th>
            <th colspan=2 ><b>'.$some_row["Total"].'</b></th>
            </tr>';
}
$output .= "</table>";
echo $output;
exit(0);

?>