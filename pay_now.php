<?php
    require('admin/inc/db_config.php');
    require('admin/inc/essentials.php');

    session_start();
    
    if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('index.php');
    }

    if(isset($_POST['pay_now'])){
        $frm_data=filteration($_POST);
        $query1="INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`) VALUES (?,?,?,?)";
        insert($query1,[$_SESSION['uId'],$_SESSION['room']['id'],$frm_data['checkin'],$frm_data['checkout']],'iiss');

        $booking_id=mysqli_insert_id($con);

        $query2="INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`,`user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
        
        insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],$_SESSION['room']['payment'],$frm_data['name'],$frm_data['phonenum'],$frm_data['address'],],'issssss');
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/common.css">
    <title>Processing</title>
    <style>
        div{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="bg-light">
    <h1>please do not refresh this page...</h1>
    <div>
        <h1 class="h-font bg-dark text-white py-3">PAY WITH</h1>
        <form action="https://uat.esewa.com.np/epay/main" method="POST">
        <input value="<?php echo $_SESSION['room']['payment'] ;?>" name="tAmt" type="hidden">
        <input value="<?php echo $_SESSION['room']['payment'] ;?>" name="amt" type="hidden">
        <input value="0" name="txAmt" type="hidden">
        <input value="0" name="psc" type="hidden">
        <input value="0" name="pdc" type="hidden">
        <input value="EPAYTEST" name="scd" type="hidden">
        <input value="<?php echo $booking_id;?>" name="pid" type="hidden">
        <input value="http://localhost/hotel_booking_system/esewa_payment_success.php" type="hidden" name="su">
        <input value="http://localhost/hotel_booking_system/esewa_payment_failed.php" type="hidden" name="fu">
        <input type="image" src="images/esewa.png" height="60px" width="150px">
        </form>
    </div>
    
</body>
</html>