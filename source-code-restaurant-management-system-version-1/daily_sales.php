<?php
include('rms.php');
session_start();
$object = new rms();
if(!$object->is_login())
{
    header("location:".$object->base_url."");
}
$output = '<table width="100%" border="1" cellpadding="5" cellspacing="5" style="font-family:Arial, san-sarif">';
$object->query = "
SELECT * FROM order_table
WHERE order_status = 'In Process'
	";

$cash_data = $object->get_result();
$output .= '<tr>
                <th width="10%">Sr#</th>
                <th width="10%">Date</th>
                <th width="10%">Room Number</th>
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
                <td>'.$row["order_number"].'</td>
                <td>'.$row["order_net_amount"].'</td>
            </tr>';

}

$output .= "</table>";
echo $output;
exit(0);

?>