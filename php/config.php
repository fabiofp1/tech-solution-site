<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$host   = 'localhost';
$user   = 'root';    
$pass   = '';        
$dbname = 'projeto';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO(
      $dsn,
      $user,
      $pass,
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro de conexão com o banco: " . $e->getMessage());
}
