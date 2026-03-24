<?php
// includes/menu.php - Menu lateral
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="logo-area">
        <div class="logo-icon">
            <i class="fas fa-building"></i>
        </div>
        <div class="logo-text">
            <h2>e-SGP</h2>
            <span>Gestão de Patrimônio</span>
        </div>
    </div>
    
    <nav class="menu">
        <a href="dashboard.php" class="menu-item <?php echo ($pagina_atual == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> <span>Dashboard</span>
        </a>
        <a href="secretarias.php" class="menu-item <?php echo ($pagina_atual == 'secretarias.php') ? 'active' : ''; ?>">
            <i class="fas fa-landmark"></i> <span>Secretarias</span>
        </a>
        <a href="unidades.php" class="menu-item <?php echo ($pagina_atual == 'unidades.php') ? 'active' : ''; ?>">
            <i class="fas fa-school"></i> <span>Unidades</span>
        </a>
        <a href="locais.php" class="menu-item <?php echo ($pagina_atual == 'locais.php') ? 'active' : ''; ?>">
            <i class="fas fa-door-open"></i> <span>Locais</span>
        </a>
        <a href="itens.php" class="menu-item <?php echo ($pagina_atual == 'itens.php') ? 'active' : ''; ?>">
            <i class="fas fa-boxes"></i> <span>Patrimônio</span>
        </a>
        <a href="leiloes.php" class="menu-item <?php echo ($pagina_atual == 'leiloes.php') ? 'active' : ''; ?>">
            <i class="fas fa-gavel"></i> <span>Leilões</span>
        </a>
        <a href="relatorios.php" class="menu-item <?php echo ($pagina_atual == 'relatorios.php') ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> <span>Relatórios</span>
        </a>
        <a href="etiquetas.php" class="menu-item <?php echo ($pagina_atual == 'etiquetas.php') ? 'active' : ''; ?>">
            <i class="fas fa-tag"></i> <span>Etiquetas</span>
        </a>
    </nav>
    
    <div class="user-info">
        <div class="user-avatar">
            <?php 
            $usuario = getUsuario();
            echo strtoupper(substr($usuario['nome'], 0, 2)); 
            ?>
        </div>
        <div class="user-details">
            <h4><?php echo $usuario['nome']; ?></h4>
            <p><?php echo $usuario['email']; ?></p>
        </div>
        <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</aside>

<main class="main-content">