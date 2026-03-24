<?php
// includes/auth.php - Verifica se usuário está logado

session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../login.php?erro=2');
        exit;
    }
}

function usuarioLogado() {
    return isset($_SESSION['usuario_id']);
}

function getUsuario() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nome' => $_SESSION['usuario_nome'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'nivel' => $_SESSION['usuario_nivel'] ?? null
    ];
}
?>