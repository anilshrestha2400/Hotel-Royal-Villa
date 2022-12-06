<?php
require('../admin/inc/essentials.php');
require('../admin/inc/db_config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $v_code)
{
    require '../PHPMailer/Exception.php';
    require '../PHPMailer/SMTP.php';
    require '../PHPMailer/PHPMailer.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'anilshrestha2400@gmail.com';                     //SMTP username
        $mail->Password   = 'wkisiiidazcsyoru';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('anilshrestha2400@gmail.com', 'Hotel Royal Villa');
        $mail->addAddress($email);     //Add a recipient
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Email verification from Hotel Royal Villa';
        $mail->Body    = "Thanks for a registration in our hotel!!!<br>
        Click the link below to verify the email address<br>
        <a href='http://localhost/hotel_booking_system/verify.php?email=$email&v_code=$v_code'>Verify</a>";
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }

}

function sendForgotMail($email, $reset_token)
{
    require '../PHPMailer/Exception.php';
    require '../PHPMailer/SMTP.php';
    require '../PHPMailer/PHPMailer.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'anilshrestha2400@gmail.com';                     //SMTP username
        $mail->Password   = 'wkisiiidazcsyoru';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('anilshrestha2400@gmail.com', 'Hotel Royal Villa');
        $mail->addAddress($email);     //Add a recipient
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Password Reset Link from Hotel Royal Villa';
        $mail->Body    = "We got a request from you to reset your password!!!<br>
        Click the link below to reset your password<br>
        <a href='http://localhost/hotel_booking_system/updatepassword.php?email=$email&reset_token=$reset_token'>Reset Password</a>";
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }

}

if (isset($_POST['register'])) {
    $data = filteration($_POST);

    //match passowrd
    if ($data['pass'] != $data['cpass']) {
        echo 'pass_mismatch';
        exit;
    }

    //check user exists or not
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", [$data['email'], $data['phonenum']], 'ss');

    if (mysqli_num_rows($u_exist) != 0) {
        $u_exist_fetch = mysqli_fetch_assoc($u_exist);
        echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
        exit;
    }

    //upload user image to server
    $img = uploadUserImage($_FILES['profile']);

    if ($img == 'inv_img') {
        echo 'inv_img';
        exit;
    } else if ($img == 'upd_failed') {
        echo 'upd_failed';
        exit;
    }

    //send confirm link to user email  later if sendgrip account accessed

    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);
    $v_code = bin2hex(random_bytes(16));

    $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`,`is_verified`, `v_code`) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['pincode'], $data['dob'], $img, $enc_pass, '0', $v_code];

    if (insert($query, $values, 'ssssssssis') && sendMail($_POST['email'],$v_code)) {
        echo 1;
    } else {
        echo 'ins_failed';
    }
}

if (isset($_POST['login'])) {

    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1", [$data['email_mob'], $data['email_mob']], 'ss');

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            if (!password_verify($data['pass'], $u_fetch['password'])) {
                echo 'invalid_pass';
            } else {
                session_start();
                $_SESSION['login'] = true;
                $_SESSION['uId'] = $u_fetch['id'];
                $_SESSION['uName'] = $u_fetch['name'];
                $_SESSION['uPic'] = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];
                echo 1;
            }
        }
    }
}

if (isset($_POST['forgot_pass'])) {
    $data = filteration($_POST);

    $u_exist = select("SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1", [$data['email']], 's');

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email';
    }
    else{
        $u_fetch = mysqli_fetch_assoc($u_exist);
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } 
        else{
            $reset_token= bin2hex(random_bytes(16));
            date_default_timezone_set('Asia/kathmandu');
            $date=date("Y-m-d");
            $query="UPDATE `user_cred` SET `resettoken`='$reset_token',`resettokenexpire`='$date' WHERE `email`='$_POST[email]'" ;
            if(mysqli_query($con,$query) && sendForgotMail($_POST['email'],$reset_token)){
                echo 'sent';
            }
            else{
                echo 'no_email';
            }
        }
    }
}