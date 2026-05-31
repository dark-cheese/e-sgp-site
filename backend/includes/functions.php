<?php
// backend/includes/functions.php
// Funções auxiliares

function gerarNumeroPatrimonio($db, $prefix = '2026') {
    $query = "SELECT COUNT(*) as total FROM itens WHERE numero_patrimonio LIKE :prefix";
    $stmt = $db->prepare($query);
    $like = $prefix . '%';
    $stmt->bindParam(":prefix", $like);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $numero = $result['total'] + 1;
    return $prefix . '.' . str_pad($numero, 3, '0', STR_PAD_LEFT);
}

function formatarValor($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function tratarData($data) {
    if(empty($data)) return null;
    $partes = explode('/', $data);
    if(count($partes) == 3) {
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }
    return $data;
}

function getUsuarioSessaoId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 1;
}

function registrarHistorico($conn, $acao, $tabelaAlvo, $registroId, $descricao) {
    try {
        $usuarioId = getUsuarioSessaoId();
        $query = 'INSERT INTO historico (usuarioId, acao, tabelaAlvo, registroId, descricao, dataRegistro) VALUES (:usuarioId, :acao, :tabelaAlvo, :registroId, :descricao, :dataRegistro)';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':acao', $acao, PDO::PARAM_STR);
        $stmt->bindValue(':tabelaAlvo', $tabelaAlvo, PDO::PARAM_STR);
        $stmt->bindValue(':registroId', $registroId, PDO::PARAM_INT);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':dataRegistro', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log('Erro ao registrar histórico: ' . $e->getMessage());
    }
}
?>