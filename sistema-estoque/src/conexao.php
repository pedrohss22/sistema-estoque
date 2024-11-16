<?php
// src/conexao.php
$host = '45.152.46.204';
$dbname = 'u105094132_db_pedro_ph';
$user = 'u105094132_pedro_ph';     // usuário padrão do MySQL
$password = 'g3H!@jN2#bF%zP8&xY7Qw$rK1^m';     // senha padrão do MySQL, deixe vazio se não tiver configurado uma senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>



