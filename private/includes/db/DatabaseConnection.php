<?php   // 데이터베이스 접속 코드    

require_once __DIR__ . '/' . './DatabaseLogin.php';
    
$pdo = new PDO($DB_ATTR, $DB_USER, $DB_PASSWORD);    
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
    
?>    
