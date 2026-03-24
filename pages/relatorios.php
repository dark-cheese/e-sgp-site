<?php
$titulo = "Relatórios";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

// Estatísticas
$sql_total = "SELECT COUNT(*) as total, SUM(valor) as total_valor FROM itens";
$res_total = mysqli_query($conexao, $sql_total);
$total = mysqli_fetch_assoc($res_total);

$sql_manutencao = "SELECT COUNT(*) as total FROM itens WHERE estado = 'regular' OR estado = 'ruim'";
$res_manutencao = mysqli_query($conexao, $sql_manutencao);
$manutencao = mysqli_fetch_assoc($res_manutencao)['total'];

$sql_inserviveis = "SELECT COUNT(*) as total FROM itens WHERE estado = 'inservivel'";
$res_inserviveis = mysqli_query($conexao, $sql_inserviveis);
$inserviveis = mysqli_fetch_assoc($res_inserviveis)['total'];

// Gráfico por secretaria
$sql_grafico = "SELECT s.nome, COUNT(i.id) as total 
                FROM secretarias s
                LEFT JOIN unidades u ON s.id = u.secretaria_id
                LEFT JOIN locais l ON u.id = l.unidade_id
                LEFT JOIN itens i ON l.id = i.local_id
                GROUP BY s.id";
$res_grafico = mysqli_query($conexao, $sql_grafico);

// Gráfico por estado
$sql_estados = "SELECT estado, COUNT(*) as total FROM itens GROUP BY estado";
$res_estados = mysqli_query($conexao, $sql_estados);
$estados_total = mysqli_fetch_assoc($res_total)['total'];

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Relatórios</h1>
        <div class="breadcrumb">Início / Relatórios</div>
    </div>
</header>

<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card"><div class="stat-info"><h3>Total Bens</h3><div class="stat-number"><?php echo $total['total']; ?></div></div></div>
    <div class="stat-card"><div class="stat-info"><h3>Valor Total</h3><div class="stat-number">R$ <?php echo number_format($total['total_valor'], 2, ',', '.'); ?></div></div></div>
    <div class="stat-card"><div class="stat-info"><h3>Manutenção</h3><div class="stat-number"><?php echo $manutencao; ?></div></div></div>
    <div class="stat-card"><div class="stat-info"><h3>Inservíveis</h3><div class="stat-number"><?php echo $inserviveis; ?></div></div></div>
</div>

<div class="chart-card" style="margin-bottom: 20px;">
    <h3>Bens por Secretaria</h3>
    <div class="progress-list">
        <?php while($row = mysqli_fetch_assoc($res_grafico)): ?>
        <div class="progress-item">
            <div class="progress-header">
                <span><?php echo $row['nome']; ?></span>
                <span><?php echo $row['total']; ?> itens</span>
            </div>
            <div class="progress-bar-bg">
                <?php $percentual = ($total['total'] > 0) ? ($row['total'] / $total['total'] * 100) : 0; ?>
                <div class="progress-bar-fill" style="width: <?php echo $percentual; ?>%"></div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="chart-card" style="margin-bottom: 20px;">
    <h3>Bens por Estado de Conservação</h3>
    <div class="progress-list">
        <?php 
        $estados_labels = ['otimo'=>'Ótimo', 'bom'=>'Bom', 'regular'=>'Regular', 'ruim'=>'Ruim', 'inservivel'=>'Inservível'];
        $estados_cores = ['otimo'=>'#27AE60', 'bom'=>'#2A7DE1', 'regular'=>'#F1C40F', 'ruim'=>'#E74C3C', 'inservivel'=>'#2C3E50'];
        while($row = mysqli_fetch_assoc($res_estados)):
        ?>
        <div class="progress-item">
            <div class="progress-header">
                <span><?php echo $estados_labels[$row['estado']]; ?></span>
                <span><?php echo $row['total']; ?> itens</span>
            </div>
            <div class="progress-bar-bg">
                <?php $percentual = ($total['total'] > 0) ? ($row['total'] / $total['total'] * 100) : 0; ?>
                <div class="progress-bar-fill" style="width: <?php echo $percentual; ?>%; background: <?php echo $estados_cores[$row['estado']]; ?>"></div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="action-buttons" style="display: flex; gap: 15px; justify-content: flex-end;">
    <a href="javascript:void(0)" onclick="alert('Funcionalidade: Gerar PDF')" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Gerar PDF</a>
    <a href="javascript:void(0)" onclick="alert('Funcionalidade: Exportar Excel')" class="btn btn-outline"><i class="fas fa-file-excel"></i> Exportar Excel</a>
</div>

<?php include_once '../includes/rodape.php'; ?>