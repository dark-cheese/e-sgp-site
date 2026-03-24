<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}

include_once '../conexao.php';

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $edital = $_POST['edital'];
    $data_leilao = $_POST['data_leilao'] ?: 'NULL';
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    
    $sql = "UPDATE leiloes SET edital='$edital', data_leilao=" . ($data_leilao != 'NULL' ? "'$data_leilao'" : "NULL") . ", status='$status', observacoes='$observacoes' WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM leiloes WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_POST['incluir_item'])) {
    $item_id = $_POST['incluir_item'];
    $edital = $_POST['edital'];
    $data_leilao = $_POST['data_leilao'] ?: 'NULL';
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    
    $sql = "INSERT INTO leiloes (edital, data_leilao, status, observacoes) VALUES ('$edital', " . ($data_leilao != 'NULL' ? "'$data_leilao'" : "NULL") . ", '$status', '$observacoes')";
    mysqli_query($conexao, $sql);
    $leilao_id = mysqli_insert_id($conexao);
    
    $sql_item = "INSERT INTO itens_leilao (leilao_id, item_id) VALUES ($leilao_id, $item_id)";
    mysqli_query($conexao, $sql_item);
    
} else {
    $edital = $_POST['edital'];
    $data_leilao = $_POST['data_leilao'] ?: 'NULL';
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    
    $sql = "INSERT INTO leiloes (edital, data_leilao, status, observacoes) VALUES ('$edital', " . ($data_leilao != 'NULL' ? "'$data_leilao'" : "NULL") . ", '$status', '$observacoes')";
    mysqli_query($conexao, $sql);
}

header('Location: ../pages/leiloes.php');
exit;
?>