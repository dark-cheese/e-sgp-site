<?php
$host = "localhost";
$user = "root";
$senha = "";
$dbase = "esgp";

$conexao = mysqli_connect($host, $user, $senha, $dbase);

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8");
?>