<?php
// backend/api/gerar_hash.php
// UTILITÁRIO: Gera o hash bcrypt correto para a senha admin123
// USO: Acesse este arquivo pelo navegador UMA VEZ para obter o hash,
//      depois atualize o banco e DELETE este arquivo por segurança.

// Gera o hash
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Exibe o resultado e o SQL pronto para copiar
echo "<pre>";
echo "Senha: $senha\n";
echo "Hash gerado: $hash\n\n";
echo "-- Cole este SQL no seu banco de dados:\n";
echo "UPDATE usuario SET senha = '$hash' WHERE email = 'admin@prefeitura.br';\n";
echo "\n-- Ou use no INSERT do dados-iniciais.sql:\n";
echo "INSERT INTO usuario (nome, email, senha, tipoUsuarioId) VALUES\n";
echo "('Administrador', 'admin@prefeitura.br', '$hash', 1);\n";
echo "</pre>";

// Verifica se o hash funciona
echo "<hr>";
echo "Verificação: " . (password_verify($senha, $hash) ? "✅ Hash válido!" : "❌ Hash inválido!") . "\n";
echo "<br><strong>⚠️ DELETE este arquivo após usar!</strong>";
?>
