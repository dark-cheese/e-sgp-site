<?php
session_start();
include_once('conexao.php');

$email = $_POST['email'];
$senha = $_POST['senha'];

// Buscar usuário no banco
$sql = "SELECT id, nome, email, senha, nivel FROM usuarios WHERE email = '$email'";
$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) == 1) {
    $usuario = mysqli_fetch_assoc($resultado);
    
    // DEBUG: Ver o que está vindo do banco
    // echo "Senha do banco: " . $usuario['senha'] . "<br>";
    // echo "Senha digitada: " . $senha . "<br>";
    // exit;
    
    // Comparação direta em texto puro
    if ($senha == $usuario['senha']) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nivel'] = $usuario['nivel'];
        
        header('Location: pages/dashboard.php');
        exit;
    }
}

header('Location: login.php?erro=1');
exit;
?>