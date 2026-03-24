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
    $unidade_id = $_POST['unidade_id'];
    $descricao = $_POST['descricao'];
    
    $sql = "UPDATE locais SET nome='$nome', unidade_id='$unidade_id', descricao='$descricao' WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM locais WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} else {
    $nome = $_POST['nome'];
    $unidade_id = $_POST['unidade_id'];
    $descricao = $_POST['descricao'];
    
    $sql = "INSERT INTO locais (nome, unidade_id, descricao) VALUES ('$nome', '$unidade_id', '$descricao')";
    mysqli_query($conexao, $sql);
}

header('Location: ../pages/locais.php');
exit;
?>