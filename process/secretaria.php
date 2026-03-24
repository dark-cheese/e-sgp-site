<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}

include_once '../conexao.php';

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $responsavel = $_POST['responsavel'];
    $descricao = $_POST['descricao'];
    
    $sql = "UPDATE secretarias SET nome='$nome', responsavel='$responsavel', descricao='$descricao' WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM secretarias WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} else {
    $nome = $_POST['nome'];
    $responsavel = $_POST['responsavel'];
    $descricao = $_POST['descricao'];
    
    $sql = "INSERT INTO secretarias (nome, responsavel, descricao, municipio_id) VALUES ('$nome', '$responsavel', '$descricao', 1)";
    mysqli_query($conexao, $sql);
}

header('Location: ../pages/secretarias.php');
exit;
?>