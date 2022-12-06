<?php
    $hname='localhost';
    $uname='root';
    $pass='';
    $db='hotelbook';

    $con=mysqli_connect($hname,$uname,$pass,$db);

    if(!$con){
        die("Cannot connect to Database".mysqli_connect_error());
    }

    function filteration($data)
    {
        foreach($data as $key => $value){
            $value=trim($value);
            $value=stripslashes($value);
            $value=strip_tags($value);
            $value=htmlspecialchars($value);         
            $data[$key]=$value;
        }
        return $data;
    }

    function select($sql,$values,$datatypes){
        $con= $GLOBALS['con'];
        if($stmt=mysqli_prepare($con,$sql)){
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res=mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
                return $res;
            }else{
                die("Query cannot be executed - SELECT");
            }
            
        }else{
            die("Query cannot be prepared- SELECT");
        }
    }

    function insert($sql,$values,$datatypes){
        $con= $GLOBALS['con'];
        if($stmt=mysqli_prepare($con,$sql)){
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res=mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                return $res;
            }else{
                die("Query cannot be executed - INSERT");
            }
            
        }else{
            die("Query cannot be prepared- INSERT");
        }
    }

    function update($sql,$values,$datatypes){
        $con= $GLOBALS['con'];
        if($stmt=mysqli_prepare($con,$sql)){
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res=mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                return $res;
            }else{
                die("Query cannot be executed - UPDATE");
            }
            
        }else{
            die("Query cannot be prepared- UPDATE");
        }
    }

    function selectAll($table){
        $con=$GLOBALS['con'];
        $res=mysqli_query($con,"SELECT * FROM $table");
        return $res;
    }

    function delete($sql,$values,$datatypes){
        $con= $GLOBALS['con'];
        if($stmt=mysqli_prepare($con,$sql)){
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res=mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                return $res;
            }else{
                die("Query cannot be executed - DELETE");
            }
            
        }else{
            die("Query cannot be prepared- DELETE");
        }
    }
?>