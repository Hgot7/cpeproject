<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cpepms";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
    } catch(PDOException $e) {
        // echo  '<div class="alert alert-danger text-center" role="alert">Connection failed:'. $e->getMessage() .'</div>';
        $e->getMessage();
    }
?>