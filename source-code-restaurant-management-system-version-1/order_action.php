<?php

//order_action.php

include('rms.php');
session_start();
$object = new rms();
date_default_timezone_set('Asia/Kolkata');

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'reset')
	{
		$object->query = "
		SELECT * FROM table_data 
		WHERE table_status = 'Enable' 
		ORDER BY table_id ASC
		";

		$table_result = $object->get_result();

		$html = '';

		foreach($table_result as $table)
		{
			$object->query = "
			SELECT * FROM order_table 
			WHERE order_table = '".$table['table_name']."' 
			AND order_status = 'In Process'
			";
			
			$object->execute();
			$order_result = $object->get_result();

			if($object->row_count() > 0)
			{
				$order_result = $object->statement_result();
				foreach($order_result as $order)
				{
					$html .= '
					<button type="button" name="table_button" id="table_'.$table["table_id"].'" class="btn btn-warning mb-4 table_button" data-index="'.$table["table_id"].'" data-order_id="'.$order["order_id"].'" status=In_Process  data-table_name="'.$table["table_name"].'">'.$table["table_name"].'<br /> </button>
					';
				}
			}
			else
			{
				$html .= '
				<button type="button" name="table_button" id="table_'.$table["table_id"].'" class="btn btn-secondary mb-4 table_button" data-index="'.$table["table_id"].'" status=Not_In_Process  data-order_id="0" data-table_name="'.$table["table_name"].'">'.$table["table_name"].'<br /> </button>
				';
			}
		}
		echo $html;
	}

	if($_POST["action"] == 'load_product')
	{
		$object->query = "
		SELECT * FROM product_table 
		WHERE category_name = '".$_POST['category_name']."' 
		AND product_status = 'Enable'
		";
		$result = $object->get_result();
		$html = '<option value="">Select Product</option>';
		foreach($result as $row)
		{
			$html .= '<option value="'.$row["product_name"].'" data-price="'.$row["product_price"].'">'.$row["product_name"].'</option>';
		}
		echo $html;
	}

	if($_POST["action"] == 'Add')
	{
		if($_POST['hidden_order_id'] > 0)
		{
			$product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];

			$item_data = array(
				':order_id'			=>	$_POST['hidden_order_id'],
				':product_name'		=>	$_POST['product_name'],
				':product_quantity'	=>	$_POST['product_quantity'],
				':product_rate'		=>	$_POST['hidden_product_rate'],
				':product_amount'	=>	$product_amount
			);

			$object->query = "
			INSERT INTO order_item_table
			(order_id, product_name, product_quantity, product_rate, product_amount)
			VALUES (:order_id, :product_name, :product_quantity, :product_rate, :product_amount)
			";
			$object->execute($item_data);


			$object->query = "
			SELECT * from order_table
			WHERE order_id = '".$_POST['hidden_order_id']."'
			";

			$old_result = $object->get_result();
			$gross_total = 0;
            $total_discount = 0;
            $total_tax_amt = 0;
            $net_total = 0;

			foreach($old_result as $row)
			{
			    $gross_total = $row["order_gross_amount"] + $product_amount;
                $other_object = new rms();
                $other_object->query = "
                SELECT * FROM tax_table
                WHERE tax_status = 'Enable'
                ORDER BY tax_id ASC
                ";

                $tax_result = $other_object->get_result();

//                 $other_object->execute();

                foreach($tax_result as $tax)
                {
                    $tax_amt = ($gross_total * $tax["tax_percentage"])/100;
                    if(strpos($tax["tax_name"], "discount") !== false)
                    {
                        $total_discount += $tax_amt;
                    }
                    else
                    {
                        $total_tax_amt += $tax_amt;
                    }
                    $tax_data = array(
                        ':order_id'				=>	$_POST['hidden_order_id'],
                        ':order_tax_name'		=>	$tax["tax_name"],
                        ':order_tax_percentage'	=>	$tax["tax_percentage"],
                        ':order_tax_amount'		=>	$tax_amt
                    );


                    $other_object->query = "
                    INSERT INTO order_tax_table
                    (order_id, order_tax_name, order_tax_percentage, order_tax_amount)
                    VALUES (:order_id, :order_tax_name, :order_tax_percentage, :order_tax_amount)
                    ";

                    $other_object->execute($tax_data);
                }
                $net_total = $gross_total + $total_tax_amt - $total_discount;


            }

			$object->query = "
            UPDATE order_table
            SET order_gross_amount = $gross_total,
            order_net_amount = $net_total
            WHERE order_id = '".$_POST['hidden_order_id']."'
            ";
            $object->execute();

            echo $_POST['hidden_order_id'];
		}
		else
		{
		    $order_no = $object->Generate_order_no();
			$order_data = array(
				':order_number'			=>	$order_no,
				':order_table'			=>	$_POST['hidden_table_name'],
				':order_gross_amount'	=>	0,
				':order_tax_amount'		=>	0,
				':order_net_amount'		=>	0,
				':order_date'			=>	date('Y-m-d'),
				':order_time'			=>	date('H:i:s'),
 				':order_waiter'			=>	'',
				':order_cashier'		=>	'',
				':order_status'			=>	'In Process',
 				':room_number'			=>	0,
 				':payment_method'		=>	'',
 				':Cash'                 =>  0,
 				':Card'                 =>  0,
 				':Upi'                  =>  0
			);

			$object->query = "
			INSERT INTO order_table 
			(order_number, order_table, order_gross_amount, order_tax_amount, order_net_amount, order_date, order_time, order_cashier, order_status, order_waiter, room_number, payment_method, Cash, Card, Upi)
			VALUES (:order_number, :order_table, :order_gross_amount, :order_tax_amount, :order_net_amount, :order_date, :order_time, :order_cashier, :order_status, :order_waiter, :room_number, :payment_method, :Cash, :Card, :Upi)
			";
			$object->execute($order_data);
			$order_id = $object->connect->lastInsertId();
			echo  $order_id;
			$product_amount = $_POST['product_quantity'] * $_POST['hidden_product_rate'];
			$item_data = array(
				':order_id'			=>	$order_id,
				':product_name'		=>	$_POST['product_name'],
				':product_quantity'	=>	$_POST['product_quantity'],
				':product_rate'		=>	$_POST['hidden_product_rate'],
				':product_amount'	=>	$product_amount
			);
			$object->query = "
			INSERT INTO order_item_table
			(order_id, product_name, product_quantity, product_rate, product_amount)
			VALUES (:order_id, :product_name, :product_quantity, :product_rate, :product_amount)
			";
			$object->execute($item_data);

			$object->query = "
			SELECT * from order_table
			WHERE order_id = '".$order_id."'
			";

			$old_result = $object->get_result();
			$gross_total = 0;
            $total_discount = 0;
            $total_tax_amt = 0;
            $net_total = 0;

			foreach($old_result as $row)
			{
			    $gross_total = $row["order_gross_amount"] + $product_amount;
                $other_object = new rms();
                $other_object->query = "
                SELECT * FROM tax_table
                WHERE tax_status = 'Enable'
                ORDER BY tax_id ASC
                ";

                $tax_result = $other_object->get_result();

                foreach($tax_result as $tax)
                {
                    $tax_amt = ($gross_total * $tax["tax_percentage"])/100;
                    if(strpos($tax["tax_name"], "discount") !== false)
                    {
                        $total_discount += $tax_amt;
                    }
                    else
                    {
                        $total_tax_amt += $tax_amt;
                    }
                    $tax_data = array(
                        ':order_id'				=>	$row["order_id"],
                        ':order_tax_name'		=>	$tax["tax_name"],
                        ':order_tax_percentage'	=>	$tax["tax_percentage"],
                        ':order_tax_amount'		=>	$tax_amt
                    );

                    $other_object->query = "
                    INSERT INTO order_tax_table
                    (order_id, order_tax_name, order_tax_percentage, order_tax_amount)
                    VALUES (:order_id, :order_tax_name, :order_tax_percentage, :order_tax_amount)
                    ";

                    $other_object->execute($tax_data);
                }
                $net_total = $gross_total + $total_tax_amt - $total_discount;

            }

			$object->query = "
            UPDATE order_table
            SET order_gross_amount = $gross_total,
            order_net_amount = $net_total
            WHERE order_id = '".$order_id."'
            ";
            $object->execute();
		}
	}

	if($_POST["action"] == "fetch_order")
	{

		$object->query = "
		SELECT * FROM order_item_table 
		WHERE order_id = '".$_POST['order_id']."' 
		ORDER BY order_item_id ASC
		";
		$result = $object->get_result();

        $object->query = "
		SELECT * FROM order_table
		WHERE order_id = '".$_POST['order_id']."'
		";
		$result_table = 0;
		$result_table = $object->get_result();
		$html = '
		<table class="table table-striped table-bordered">
			<tr>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Rate</th>
				<th>Amount</th>
				<th>Action</th>
			</tr>
		';
		foreach($result as $row)
		{
			$html .= '
			<tr>
				<td>'.$row["product_name"].'</td>
				<td><input type="number" class="form-control product_quantity" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'" data-rate="'.$row["product_rate"].'" min="1" max="25" value="'.$row["product_quantity"].'" /></td>
				<td>'.$object->cur . $row["product_rate"].'</td>
				<td><span id="product_amount_'.$row["order_item_id"].'">'.$object->cur . $row["product_amount"].'</span></td>
				<td><button type="button" name="remove" class="btn btn-danger btn-sm remove_item" data-item_id="'.$row["order_item_id"].'" data-order_id="'.$row["order_id"].'"><i class="fas fa-minus-square"></i></button></td>
			</tr>
			';



		}

        foreach($result_table as $row){
		$html .= '<div>Table Number:   '.$row["order_table"].'</div><br />';
		$html .= '<div ><label>Room number <label><input type="text" name="room_number" id="room_number" class="form-control room_number" value="'.$row["room_number"].'" data-order_id="'.$row["order_id"].'"/></div>';
		$html .= '<div ><label>Waiter name <label><input type="text" name="waiter_name" id="waiter_name" class="form-control waiter_name'.'" data-order_id="'.$row["order_id"].'" value="'.$row["order_waiter"].'"/></div>';

        $object->query = "
		SELECT * FROM table_data
		WHERE table_name = '".$row["order_table"]."'";

        $result = $object->get_result();
        foreach($result as $table){
        $html .= '<form method="post" id="order_form"><input type="submit" id="submit_button"  name="add_item" value="Add item" id="table_'.$table["table_id"].'" class="add_item btn btn-success" data-index="'.$table["table_id"].'" data-order_id="'.$row["order_id"].'" data-table_name="'.$table["table_name"].'" /></form>';
        }

        }

		$html .= '
		</table>
		';
		echo $html;
	}

	if($_POST['action'] == 'change_quantity')
	{

	    $object->query = "
		SELECT * FROM order_item_table
		WHERE order_id = '".$_POST["order_id"]."'
		AND order_item_id = '".$_POST["item_id"]."'
		";

        $result = $object->get_result();
        $product_amount = 0;
        $gross_amount = 0;
        foreach($result as $row){
        $product_amount = $row["product_amount"];
        }

        $object->query = "
		SELECT * FROM order_table
		WHERE order_id = '".$_POST["order_id"]."'
		";
        $result = $object->get_result();
        foreach($result as $row){
        $gross_amount = $row["order_gross_amount"];
        }

		$object->query = "
		UPDATE order_item_table 
		SET product_quantity = '".$_POST["quantity"]."', 
		product_amount = '".$_POST["quantity"] * $_POST["rate"]."' 
		WHERE order_id = '".$_POST["order_id"]."' 
		AND order_item_id = '".$_POST["item_id"]."'
		";
		$object->execute();

		$gross_amount = $gross_amount - $product_amount + $_POST["quantity"] * $_POST["rate"];

        $object->query = "
        SELECT * FROM tax_table
        WHERE tax_status = 'Enable'
        ORDER BY tax_id ASC
        ";
        $total_discount = 0;
        $total_tax_amt = 0;
        $tax_result = $object->get_result();

        foreach($tax_result as $tax)
        {
            $tax_amt = ($gross_amount * $tax["tax_percentage"])/100;
            if(strpos($tax["tax_name"], "discount") !== false)
            {
                $total_discount += $tax_amt;
            }
            else
            {
                $total_tax_amt += $tax_amt;
            }
            $tax_data = array(
                ':order_id'				=>	$_POST['order_id'],
                ':order_tax_name'		=>	$tax["tax_name"],
                ':order_tax_percentage'	=>	$tax["tax_percentage"],
                ':order_tax_amount'		=>	$tax_amt
            );


            $object->query = "
            INSERT INTO order_tax_table
            (order_id, order_tax_name, order_tax_percentage, order_tax_amount)
            VALUES (:order_id, :order_tax_name, :order_tax_percentage, :order_tax_amount)
            ";

            $object->execute($tax_data);
        }

        $net_total = $gross_amount + $total_tax_amt - $total_discount;



        $object->query = "
		UPDATE order_table
		SET order_gross_amount = $gross_amount,
		order_net_amount = $net_total
		WHERE order_id = '".$_POST["order_id"]."'
		";

		$object->execute();
	}

	if($_POST['action'] == 'change_room_number')
	{
		$object->query = "
		UPDATE order_table
		SET room_number = '".$_POST["room_number"]."'
		WHERE order_id = '".$_POST["order_id"]."'
		";
		$object->execute();
		echo '<div class="alert alert-success">Order Remove Successfully...</div>';
	}

	if($_POST['action'] == 'change_waiter_name')
	{
		$object->query = "
		UPDATE order_table
		SET order_waiter = '".$_POST["waiter_name"]."'
		WHERE order_id = '".$_POST["order_id"]."'
		";
		$object->execute();
		echo '<div class="alert alert-success">Order Remove Successfully...</div>';
	}

	if($_POST['action'] == 'change_payment_method')
	{
		$object->query = "
		UPDATE order_table
		SET payment_method = '".$_POST["payment_method"]."'
		WHERE order_id = '".$_POST["order_id"]."'
		";
		$object->execute();
	}

	if($_POST['action'] == 'change_cash_amount'){
	    $object->query = "
        UPDATE order_table
        SET  Cash = '".$_POST["cash"]."'
        WHERE order_id = '".$_POST["order_id"]."'
        ";
		$object->execute();
	}

	if($_POST['action'] == 'change_card_amount'){
	    $object->query = "
        UPDATE order_table
        SET  Card = '".$_POST["card"]."'
        WHERE order_id = '".$_POST["order_id"]."'
        ";
		$object->execute();
	}

	if($_POST['action'] == 'change_upi_amount'){
	    $object->query = "
        UPDATE order_table
        SET  Upi = '".$_POST["upi"]."'
        WHERE order_id = '".$_POST["order_id"]."'
        ";
		$object->execute();
	}



	if($_POST['action'] == 'remove_item')
	{

		$object->query = "
		SELECT * FROM order_item_table
		WHERE order_id = '".$_POST["order_id"]."'
		AND order_item_id = '".$_POST["item_id"]."'
		";

        $result = $object->get_result();
        $product_amount = 0;
        $gross_amount = 0;
        foreach($result as $row){
        $product_amount = $row["product_amount"];
        }

        $object->query = "
		SELECT * FROM order_table
		WHERE order_id = '".$_POST["order_id"]."'
		";
        $result = $object->get_result();
        foreach($result as $row){
        $gross_amount = $row["order_gross_amount"];
        }


		$object->query = "
		DELETE FROM order_item_table 
		WHERE order_id = '".$_POST["order_id"]."' 
		AND order_item_id = '".$_POST["item_id"]."'
		";

		$object->execute();

		$object->query = "
		SELECT order_item_id FROM order_item_table 
		WHERE order_id = '".$_POST["order_id"]."'
		";

		$object->execute();

		echo $object->row_count();

		if($object->row_count() == 0)
		{
			$object->query = "
			DELETE FROM order_table 
			WHERE order_id = '".$_POST["order_id"]."'
			";
			$object->execute();
		}

        $gross_amount = $gross_amount - $product_amount;

        $object->query = "
        SELECT * FROM tax_table
        WHERE tax_status = 'Enable'
        ORDER BY tax_id ASC
        ";
        $total_discount = 0;
        $total_tax_amt = 0;
        $tax_result = $object->get_result();

        foreach($tax_result as $tax)
        {
            $tax_amt = ($gross_amount * $tax["tax_percentage"])/100;
            if(strpos($tax["tax_name"], "discount") !== false)
            {
                $total_discount += $tax_amt;
            }
            else
            {
                $total_tax_amt += $tax_amt;
            }
            $tax_data = array(
                ':order_id'				=>	$_POST['order_id'],
                ':order_tax_name'		=>	$tax["tax_name"],
                ':order_tax_percentage'	=>	$tax["tax_percentage"],
                ':order_tax_amount'		=>	$tax_amt
            );


            $object->query = "
            INSERT INTO order_tax_table
            (order_id, order_tax_name, order_tax_percentage, order_tax_amount)
            VALUES (:order_id, :order_tax_name, :order_tax_percentage, :order_tax_amount)
            ";

            $object->execute($tax_data);
        }

        $net_total = $gross_amount + $total_tax_amt - $total_discount;



        $object->query = "
		UPDATE order_table
		SET order_gross_amount = $gross_amount,
		order_net_amount = $net_total
		WHERE order_id = '".$_POST["order_id"]."'
		";

		$object->execute();
	}

	if($_POST["action"] == 'dashboard_reset')
	{
		$object->query = "
		SELECT * FROM table_data 
		WHERE table_status = 'Enable' 
		ORDER BY table_id ASC
		";

		$table_result = $object->get_result();

		$html = '<div class="row">';

		foreach($table_result as $table)
		{
			$object->query = "
			SELECT * FROM order_table 
			WHERE order_table = '".$table['table_name']."' 
			AND order_status = 'In Process'
			";
			
			$object->execute();

			if($object->row_count() > 0)
			{
				$order_result = $object->statement_result();
				foreach($order_result as $order)
				{
					$html .= '
					<div class="col-lg-2 mb-3">
						<div class="card bg-info text-white shadow">
							<div class="card-body">
								'.$table["table_name"].'
								<div class="mt-1 text-white-50 small">Booked</div>
							</div>
						</div>
					</div>
					';
				}
			}
			else
			{
				$html .= '
				<div class="col-lg-2 mb-3">
					<div class="card bg-light text-black shadow">
						<div class="card-body">
							'.$table["table_name"].'
							<div class="mt-1 text-black-50 small"></div>
						</div>
					</div>
				</div>
				';
			}
		}
		echo $html;
	}

}


?>