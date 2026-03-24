<?php
$titulo = "Dashboard";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

// Estatísticas
$sql_total_itens = "SELECT COUNT(*) as total FROM itens";
$res_total_itens = mysqli_query($conexao, $sql_total_itens);
$total_itens = mysqli_fetch_assoc($res_total_itens)['total'];

$sql_total_secretarias = "SELECT COUNT(*) as total FROM secretarias";
$res_total_secretarias = mysqli_query($conexao, $sql_total_secretarias);
$total_secretarias = mysqli_fetch_assoc($res_total_secretarias)['total'];

$sql_total_unidades = "SELECT COUNT(*) as total FROM unidades";
$res_total_unidades = mysqli_query($conexao, $sql_total_unidades);
$total_unidades = mysqli_fetch_assoc($res_total_unidades)['total'];

// Gráfico
$sql_grafico = "SELECT s.nome, COUNT(i.id) as total 
                FROM secretarias s
                LEFT JOIN unidades u ON s.id = u.secretaria_id
                LEFT JOIN locais l ON u.id = l.unidade_id
                LEFT JOIN itens i ON l.id = i.local_id
                GROUP BY s.id";
$res_grafico = mysqli_query($conexao, $sql_grafico);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Dashboard</h1>
        <div class="breadcrumb">Início / Dashboard</div>
    </div>
    <div class="date-display">
        <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
    </div>
</header>

<div class="welcome-card">
    <h2>Olá, <?php echo $_SESSION['usuario_nome']; ?>!</h2>
    <p>Bem-vindo ao e-SGP. Aqui está o resumo do patrimônio municipal.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Secretarias</h3>
            <div class="stat-number"><?php echo $total_secretarias; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-landmark"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Unidades</h3>
            <div class="stat-number"><?php echo $total_unidades; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-school"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Itens</h3>
            <div class="stat-number"><?php echo $total_itens; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-boxes"></i></div>
    </div>
</div>

<div class="chart-card">
    <h3>Bens por Secretaria</h3>
    <div class="progress-list">
        <?php while($row = mysqli_fetch_assoc($res_grafico)): ?>
        <div class="progress-item">
            <div class="progress-header">
                <span><?php echo $row['nome']; ?></span>
                <span><?php echo $row['total']; ?> itens</span>
            </div>
            <div class="progress-bar-bg">
                <?php $percentual = ($total_itens > 0) ? ($row['total'] / $total_itens * 100) : 0; ?>
                <div class="progress-bar-fill" style="width: <?php echo $percentual; ?>%"></div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include_once '../includes/rodape.php'; ?>