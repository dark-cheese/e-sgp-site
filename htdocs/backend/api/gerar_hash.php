<?php
// UTILITÁRIO: Gera hash bcrypt para qualquer senha
// ⚠️ DELETE este arquivo após usar!

$senha = isset($_GET['senha']) ? $_GET['senha'] : 'admin123';
$hash  = password_hash($senha, PASSWORD_DEFAULT);
$ok    = password_verify($senha, $hash) ? '✅ Válido' : '❌ Inválido';
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Gerar Hash</title>
<style>body{font-family:monospace;padding:30px;background:#1a1a2e;color:#eee}
.box{background:#16213e;padding:20px;border-radius:8px;margin:10px 0}
input{padding:8px;width:300px;border-radius:4px;border:none;font-family:monospace}
button{padding:8px 20px;background:#e94560;color:#fff;border:none;border-radius:4px;cursor:pointer}
.hash{word-break:break-all;color:#0f3460;background:#a8ff78;padding:10px;border-radius:4px}
.warn{color:#ff6b6b;font-weight:bold}</style></head><body>
<h2>🔐 Gerador de Hash - e-SGP</h2>
<div class="box">
  <form method="GET">
    Senha: <input type="text" name="senha" value="<?= htmlspecialchars($senha) ?>">
    <button type="submit">Gerar</button>
  </form>
</div>
<div class="box">
  <p>Senha: <strong><?= htmlspecialchars($senha) ?></strong></p>
  <p>Hash gerado (<?= $ok ?>):</p>
  <div class="hash"><?= $hash ?></div>
</div>
<div class="box">
  <p>SQL para atualizar o banco:</p>
  <div class="hash">UPDATE usuario SET senha = '<?= $hash ?>' WHERE email = 'admin@prefeitura.br';</div>
</div>
<p class="warn">⚠️ DELETE este arquivo (gerar_hash.php) depois de usar!</p>
</body></html>
