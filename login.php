<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-SGP - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #1A3B5D; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="login-container" style="width: 100%; max-width: 360px; padding: 15px;">
        <div class="login-card" style="background: white; border-radius: 4px; padding: 25px; border: 1px solid #E1E8ED;">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="width: 60px; height: 60px; background: #2A7DE1; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <i class="fas fa-building" style="font-size: 28px; color: white;"></i>
                </div>
                <h1 style="font-size: 24px; color: #1A3B5D;">e-SGP</h1>
                <p style="font-size: 12px; color: #7F8C8D;">Sistema de Gestão de Patrimônio</p>
            </div>
            
            <?php if(isset($_GET['erro'])): ?>
                <div class="mensagem erro">
                    <?php 
                    if($_GET['erro'] == 1) echo "E-mail ou senha incorretos!";
                    if($_GET['erro'] == 2) echo "Você precisa estar logado para acessar esta página!";
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="processo_login.php">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="admin@prefeitura.br" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #E1E8ED; font-size: 12px;">
                <p>Acesso de demonstração:</p>
                <div style="background: #F5F7FA; padding: 8px; border-radius: 4px; margin-top: 8px;">
                    <strong>admin@prefeitura.br</strong> / <strong>admin123</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>