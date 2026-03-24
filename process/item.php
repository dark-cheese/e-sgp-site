<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}

include_once '../conexao.php';

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $numero_patrimonio = $_POST['numero_patrimonio'];
    $descricao = $_POST['descricao'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $estado = $_POST['estado'];
    $data_aquisicao = $_POST['data_aquisicao'] ?: 'NULL';
    $valor = $_POST['valor'] ?: 0;
    $local_id = $_POST['local_id'];
    $observacoes = $_POST['observacoes'];
    
    $sql = "UPDATE itens SET 
            numero_patrimonio='$numero_patrimonio',
            descricao='$descricao',
            marca='$marca',
            modelo='$modelo',
            numero_serie='$numero_serie',
            estado='$estado',
            data_aquisicao=" . ($data_aquisicao != 'NULL' ? "'$data_aquisicao'" : "NULL") . ",
            valor='$valor',
            local_id='$local_id',
            observacoes='$observacoes'
            WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} elseif (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM itens WHERE id=$id";
    mysqli_query($conexao, $sql);
    
} else {
    $numero_patrimonio = $_POST['numero_patrimonio'];
    $descricao = $_POST['descricao'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $estado = $_POST['estado'];
    $data_aquisicao = $_POST['data_aquisicao'] ?: 'NULL';
    $valor = $_POST['valor'] ?: 0;
    $local_id = $_POST['local_id'];
    $observacoes = $_POST['observacoes'];
    
    $sql = "INSERT INTO itens (numero_patrimonio, descricao, marca, modelo, numero_serie, estado, data_aquisicao, valor, local_id, observacoes) 
            VALUES ('$numero_patrimonio', '$descricao', '$marca', '$modelo', '$numero_serie', '$estado', " . ($data_aquisicao != 'NULL' ? "'$data_aquisicao'" : "NULL") . ", '$valor', '$local_id', '$observacoes')";
    mysqli_query($conexao, $sql);
}

header('Location: ../pages/itens.php');
exit;
?>