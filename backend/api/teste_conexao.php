<?php
// backend/api/teste_conexao.php
// Teste de conexão com o banco de dados

header("Content-Type: application/json");

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if($conn === null) {
        echo json_encode([
            "success" => false,
            "message" => "Conexão retornou null",
            "erro" => "PDOException capturada internamente"
        ]);
    } else {
        // Testa uma query simples
        $result = $conn->query("SELECT 1");
        echo json_encode([
            "success" => true,
            "message" => "Conexão estabelecida com sucesso!",
            "database" => "2026ProjetoInv",
            "host" => "200.131.251.11:3341"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao conectar",
        "erro" => $e->getMessage()
    ]);
}
?>
