<?php
date_default_timezone_set('Asia/Kolkata');
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
?>

<html>
<div class="row justify-content-center pt-4">
                <label>Date</label>
                <div>
                    <input type="date" name="date" id="new_date" onchange="foo_date()" value="<?php echo $date ?>"/>
                </div>
                <br />
                <label>Month</label>
                <div>
                    <input type="month" name="month" id="new_month" onchange="foo_month()" value="<?php echo $month ?>"/>
                </div>
                <br />
</div>

</html>
<script>
function foo_date(){
var date = document.getElementById("new_date").value;
location.replace('outstanding_report.php?date='+date+'&updated=date&month=')
}
function foo_month(){
var month = document.getElementById("new_month").value;
location.replace('outstanding_report.php?month='+month+'&updated=month&date=')
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
$object->query = "
SELECT * FROM order_table
WHERE order_status = 'In Process'
	";

if($_GET["updated"] == "date"){
$object->query = "
SELECT * FROM order_table
WHERE order_status = 'In Process'
AND order_date = '".$date."'";

$sum_object->query = "
SELECT Sum(order_net_amount) AS 'Total' FROM order_table
WHERE order_status = 'In Process'
AND order_date = '".$date."'";

}
elseif($_GET["updated"] == "month"){
$object->query = "
SELECT * FROM order_table
WHERE order_status = 'In Process'
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'";

$sum_object->query = "
SELECT Sum(order_net_amount) AS 'Total' FROM order_table
WHERE order_status = 'In Process'
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'";
}
else{
$object->query = "
SELECT * FROM order_table
WHERE order_status = 'In Process'
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'";

$sum_object->query = "
SELECT Sum(order_net_amount) AS 'Total' FROM order_table
WHERE order_status = 'In Process'
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'";
}

$cash_data = $object->get_result();
$total_amt = $sum_object->get_result();
$output .= '<tr>
                <th width="10%">Sr#</th>
                <th width="10%">Date</th>
                <th width="10%">Room Number</th>
                <th width="10%">Table Number</th>
                <th width="10%">Bill No.</th>
                <th width="15%">Bill Amount</th>
            </tr>';

$count = 0;
foreach($cash_data as $row)
{
$count++;
$output .= '<tr>
                <td>'.$count.'</td>
                <td>'.$row["order_date"].'</td>
                <td>'.$row["room_number"].'</td>
                <td>'.$row["order_table"].'</td>
                <td>'.$row["order_number"].'</td>
                <td>'.$row["order_net_amount"].'</td>
            </tr>';

}
foreach($total_amt as $some_row){
$output .= '<tr>
            <th colspan=3 ><b>Total</b></th>
            <th colspan=3 ><b>'.$some_row["Total"].'</b></th>
            </tr>';
}
$output .= "</table>";
echo $output;
exit(0);

?>