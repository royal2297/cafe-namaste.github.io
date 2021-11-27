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
location.replace('upi_report.php?date='+date+'&updated=date&month=')
}
function foo_month(){
var month = document.getElementById("new_month").value;
location.replace('upi_report.php?month='+month+'&updated=month&date=')
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
if($_GET["updated"] == "date"){
$object->query = "
SELECT * FROM order_table
WHERE Upi > 0
AND order_date = '".$date."'
AND order_status = 'Completed'";

$sum_object->query = "
SELECT Sum(Upi) AS 'Total' FROM order_table
WHERE Upi > 0
AND order_date = '".$date."'
AND order_status = 'Completed'";

}
elseif($_GET["updated"] == "month"){
$object->query = "
SELECT * FROM order_table
WHERE Upi > 0
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'
AND order_status = 'Completed'";

$sum_object->query = "
SELECT Sum(Upi) AS 'Total' FROM order_table
WHERE Upi > 0
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'
AND order_status = 'Completed'";
}
else{
$object->query = "
SELECT * FROM order_table
WHERE Upi > 0
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'
AND order_status = 'Completed'";

$sum_object->query = "
SELECT Sum(Upi) AS 'Total' FROM order_table
WHERE Upi > 0
AND Month(order_date) = '".date("m", strtotime($month))."'
AND Year(order_date) = '".date("Y", strtotime($month))."'
AND order_status = 'Completed'";
}

$upi_data = $object->get_result();
$total_amt = $sum_object->get_result();

$output .= '<tr>
                <th width="10%">Sr#</th>
                <th width="10%">Bill No.</th>
                <th width="10%">Bill Date.</th>
                <th width="15%">Bill Amount</th>
                <th width="15%">Paid by UPI</th>
            </tr>';

$count = 0;
foreach($upi_data as $row)
{
$count++;
$output .= '<tr>
                <td>'.$count.'</td>
                <td>'.$row["order_number"].'</td>
                <td>'.$row["order_date"].'</td>
                <td>'.$row["order_net_amount"].'</td>
                <td>'.$row["Upi"].'</td>
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