<?php
// backend/api/login_simple.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->senha)) {
    echo json_encode(["success" => false, "message" => "E-mail e senha são obrigatórios!"]);
    exit;
}

$email = trim($data->email);
$senha = $data->senha;

$database = new Database();
$conn     = $database->getConnection();

if ($conn === null) {
    echo json_encode([
        "success" => false,
        "message" => "Erro de conexão com o banco de dados!",
        "debug"   => $database->getUltimoErro() // remover em produção final
    ]);
    exit;
}

try {
    $query = "SELECT u.id, u.nome, u.email, u.senha, tu.nome AS tipo_usuario
              FROM usuario u
              JOIN tipo_usuario tu ON u.tipoUsuarioId = tu.id
              WHERE u.email = :email
              LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_start();
        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nivel'] = $usuario['tipo_usuario'];

        echo json_encode([
            "success" => true,
            "message" => "Login realizado com sucesso!",
            "usuario" => [
                "id"           => $usuario['id'],
                "nome"         => $usuario['nome'],
                "email"        => $usuario['email'],
                "tipo_usuario" => $usuario['tipo_usuario']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "E-mail ou senha incorretos!"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erro ao processar login: " . $e->getMessage()]);
}
?>
