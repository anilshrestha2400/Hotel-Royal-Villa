<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();
unset($_SESSION['room']);

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
	redirect('index.php');
}

if (
	isset($_REQUEST['oid']) &&
	isset($_REQUEST['amt']) &&
	isset($_REQUEST['refId'])
) {
	$sql = "SELECT * FROM `booking_details` WHERE `booking_id` = '" . $_REQUEST['oid'] . "'";
	$result = mysqli_query($con, $sql);
	if ($result) {


		if (mysqli_num_rows($result) == 1) {
			$order = mysqli_fetch_assoc($result);
			$url = "https://uat.esewa.com.np/epay/transrec";
			
			$data =[
				'amt'=> $order['total_pay'],
				'rid'=> $_REQUEST['refId'],
				'pid'=>$order['booking_id'],
				'scd'=> 'EPAYTEST'
			];

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			$response_code = get_xml_node_value('response_code', $response);

			if (trim($response_code)  == 'Success') {
				$sql = "UPDATE `booking_order` SET `booking_status`='booked',`trans_amt`='" . $order['total_pay'] . "' WHERE `booking_id`='" . $order['booking_id'] . "'";
				mysqli_query($con, $sql);
				
				//echo 'Thank you for purchasing with us. Your payment has been successfully.';
				redirect('success.php');
			}else{
				redirect('index.php');
			}
		}
	}
}


function get_xml_node_value($node, $xml)
{
	if ($xml == false) {
		return false;
	}
	$found = preg_match('#<' . $node . '(?:\s+[^>]+)?>(.*?)' .
		'</' . $node . '>#s', $xml, $matches);
	if ($found != false) {

		return $matches[1];
	}

	return false;
}
