<?php
include('rms.php');
session_start();
$object = new rms();
if(!$object->is_login())
{
    header("location:".$object->base_url."");
}
$object->query = "
SELECT product_name, SUM(product_quantity) as 'total' FROM order_item_table
GROUP BY  product_name
ORDER BY total DESC
";
$result = $object->get_result();

foreach($result as $row){
echo  $row["product_name"];
echo  "           ";
echo  $row["total"];
echo  "<br />";
}

exit(0);

?>