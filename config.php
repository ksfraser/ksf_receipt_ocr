<?php


//This can/should be refactored to use FA's code.



// Database connection for FrontAccounting
$host = "localhost";
$dbname = "frontaccounting";
$username = "FA_DB_USER"; 
$password = "FA_DB_PASS";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
