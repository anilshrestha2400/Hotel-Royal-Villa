<?php
require('admin/inc/essentials.php');
require('admin/inc/db_config.php');



if (isset($_GET['email']) && isset($_GET['v_code'])) {
    $query = "SELECT * FROM `user_cred` WHERE `email`='$_GET[email]' AND `v_code`='$_GET[v_code]'";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['is_verified'] == 0) {
                $update = "UPDATE `user_cred` SET `is_verified`='1' WHERE `email`='$result_fetch[email]'";
                if (mysqli_query($con, $update)) {
                    echo "<script>alert('Email verification successfully!!!')</script>";
                    redirect('index.php');
                } else {
                    echo "<script>alert('Cannot Verify, Server down!!!')</script>";
                    redirect('index.php');
                }
            } else {
                echo "<script>alert('Email already verified!!!')</script>";
                redirect('index.php');
            }
        } else {
            echo "<script>alert('Cannot Verify, Server down!!!')</script>";
            redirect('index.php');
        }
    }
}
?>