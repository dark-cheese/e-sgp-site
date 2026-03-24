<?php
// backend/api/login_simple.php
// Caminho: C:/xampp/htdocs/sql/e-sgp-site/backend/api/login_simple.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Pega os dados enviados pelo frontend
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email) && isset($data->senha)) {
    
    $email = $data->email;
    $senha = $data->senha;
    
    // Credenciais fixas para teste
    if($email === 'admin@prefeitura.br' && $senha === 'admin123') {
        
        // Iniciar sessão
        session_start();
        $_SESSION['usuario_id'] = 1;
        $_SESSION['usuario_nome'] = 'Administrador';
        $_SESSION['usuario_email'] = 'admin@prefeitura.br';
        $_SESSION['usuario_nivel'] = 'admin';
        
        echo json_encode([
            "success" => true,
            "message" => "Login realizado com sucesso!",
            "usuario" => [
                "id" => 1,
                "nome" => "Administrador",
                "email" => "admin@prefeitura.br",
                "nivel" => "admin"
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "E-mail ou senha incorretos!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "E-mail e senha são obrigatórios!"
    ]);
}
?>