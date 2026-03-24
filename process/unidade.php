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
    $secretaria_id = $_POST['secretaria_id'];
    $endereco = $_POST['endereco'];
    $responsavel = $_POST['responsavel'];
    
    $sql = "UPDATE unidades SET nome='$nome', secretaria_id='$secretaria_id', endereco='$endereco', responsavel='$responsavel' WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM unidades WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} else {
    $nome = $_POST['nome'];
    $secretaria_id = $_POST['secretaria_id'];
    $endereco = $_POST['endereco'];
    $responsavel = $_POST['responsavel'];
    
    $sql = "INSERT INTO unidades (nome, secretaria_id, endereco, responsavel) VALUES ('$nome', '$secretaria_id', '$endereco', '$responsavel')";
    mysqli_query($conexao, $sql);
}

header('Location: ../pages/unidades.php');
exit;
?>