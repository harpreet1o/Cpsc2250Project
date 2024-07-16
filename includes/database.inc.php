<?php
$connString = "mysql:host=localhost;dbname=project";
$user = "root";
$pass = "";
try { 
    $pdo = new PDO($connString,$user,$pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo "connection failed";
    die( $e->getMessage() );
    }
       
?>
